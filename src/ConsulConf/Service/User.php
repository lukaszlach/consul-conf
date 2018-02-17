<?php

namespace ConsulConf\Service;

use Phalcon\Crypt;
use Phalcon\Http\Response\Cookies;

class User
{
    const COOKIE_SESSION_ID  = 'cds';
    const COOKIE_SESSION_TTL = 60*60*12;

    const SESSION_KEY_VALID_UNTIL      = 'vu';
    const SESSION_KEY_CLIENT_IP        = 'ip';
    const SESSION_KEY_CONSUL_ACL_TOKEN = 'acl';
    const SESSION_KEY_USER_ID          = 'uid';

    const DEFAULT_CRYPT_KEY = '#3oj5$=ca?.vk//j2V$';
    const USER_ID_ANONYMOUS = 'anonymous';

    /** @var array */
    protected $session = [];
    /** @var Config */
    protected $config;
    /** @var Shell */
    protected $shell;
    /** @var Notify */
    protected $notify;

    public function initialize()
    {
        $this->unpack();
    }

    /**
     * @return bool
     */
    public function canLogIn()
    {
        return $this->config->isBasicAuthEnabled() || $this->config->isLdapEnabled();
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function logIn($username, $password)
    {
        $this->logOut();
        $loggedIn = !$this->canLogIn();
        if (empty($username)) {
            if ($loggedIn) {
                $username = self::USER_ID_ANONYMOUS;
            } else {

                return false;
            }
        }
        if (!$loggedIn && $this->config->isBasicAuthEnabled()) {

            $loggedIn = "$username:$password" === $this->config->getBasicAuth();
        }
        if (!$loggedIn && $this->config->isLdapEnabled()) {
            try {
                $this->shell->execForUser(
                    '/consul-conf/bin/ldap-check-credentials.sh',
                    [$username, $password]
                );
                $loggedIn = true;
            } catch (\Exception $e) {
                $loggedIn = false;
            }
        }
        if ($loggedIn) {
            $this->set(self::SESSION_KEY_USER_ID, $username);
            $this->canLogIn() && $this->notify->send('Logged in');
        }

        return $loggedIn;
    }

    public function logOut()
    {
        $this->regenerate();
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->session[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->session;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * @return string|null
     */
    public function getUserId()
    {
        return $this->get(self::SESSION_KEY_USER_ID);
    }

    /**
     * @return string|null
     */
    public function getConsulAclToken()
    {
        return $this->get(self::SESSION_KEY_CONSUL_ACL_TOKEN);
    }

    /**
     *
     */
    public function setConsulAclToken($aclToken)
    {
        $this->set(self::SESSION_KEY_CONSUL_ACL_TOKEN, $aclToken);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $validUntil   = $this->get(self::SESSION_KEY_VALID_UNTIL);
        $clientIp     = $this->get(self::SESSION_KEY_CLIENT_IP);

        return time() <= $validUntil && $this->getRealIp() === $clientIp;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        $isLoggedIn = !empty($this->getUserId()) && $this->isValid();
        // handle authorization turned on and anonymous session
        if ($isLoggedIn && $this->canLogIn() && self::USER_ID_ANONYMOUS === $this->getUserId()) {
            $this->regenerate();
            $isLoggedIn = false;
        }
        if (!$isLoggedIn && !$this->canLogIn()) {
            $this->logIn(null, null);

            return $this->isLoggedIn();
        }

        return $isLoggedIn;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Shell $shell
     */
    public function setShell(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param Notify $notify
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    /**
     *
     */
    protected function unpack()
    {
        $this->session = [];
        $sessionCookie = $_COOKIE[self::COOKIE_SESSION_ID] ?? null;
        if (null === $sessionCookie) {

            return;
        }
        $crypt = $this->getCrypt();
        $sessionData = $crypt->decryptBase64($sessionCookie);
        $sessionData = json_decode($sessionData, true);
        if (is_array($sessionData)) {
            $this->session = $sessionData;
        }
        if (empty($this->session) || !$this->isValid()) {
            $this->regenerate();

            return;
        }
    }

    /**
     * @return string
     */
    public function pack()
    {
        $sessionData = json_encode($this->session);
        $sessionData = $this->getCrypt()->encryptBase64($sessionData);

        return $sessionData;
    }

    /**
     * @return Crypt
     */
    protected function getCrypt()
    {
        $crypt = new Crypt();
        $crypt->setKey($this->config->getCryptKey() ?: self::DEFAULT_CRYPT_KEY);

        return $crypt;
    }

    /**
     *
     */
    protected function regenerate()
    {
        $this->session = [
            self::SESSION_KEY_VALID_UNTIL => time() + self::COOKIE_SESSION_TTL,
            self::SESSION_KEY_CLIENT_IP   => $this->getRealIp(),
        ];
    }

    /**
     * @return string
     */
    public function getRealIp()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}