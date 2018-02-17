<?php

namespace ConsulConf\Service;

class Notify
{
    const NOTIFY_SLACK   = 1;
    const NOTIFY_HIPCHAT = 2;
    const NOTIFY_LOG     = 4;
    const NOTIFY_ALL     = 255;

    const NOTIFY_COLOR_GRAY  = 'gray';
    const NOTIFY_COLOR_GREEN = 'green';
    const NOTIFY_COLOR_RED   = 'red';

    /** @var Shell */
    protected $shell;
    /** @var User */
    protected $user;
    /** @var Config */
    protected $config;

    /**
     * @param string $message
     * @param string $color
     * @param int $target
     */
    public function send($message, $color = self::NOTIFY_COLOR_GRAY, $target = self::NOTIFY_ALL)
    {
        ($target & self::NOTIFY_LOG)     && $this->sendLog($message);
        ($target & self::NOTIFY_HIPCHAT) && $this->sendHipchat($message, $color);
        ($target & self::NOTIFY_SLACK)   && $this->sendSlack($message, $color);
    }

    /**
     * @param string $message
     * @param string $color
     * @return bool
     */
    public function sendHipchat($message, $color = self::NOTIFY_COLOR_GRAY)
    {
        if (!$this->config->stringToBool($this->config->getEnv('NOTIFY_HIPCHAT'))) {

            return false;
        }
        try {
            $this->shell->execForUser('/consul-conf/bin/notify-hipchat.sh', [$message, $color]);
        } catch (\Exception $e) {
            $this->sendLog('Failed to send Hipchat notification');
            $this->sendLog($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @param string $color
     * @return bool
     */
    public function sendSlack($message, $color = self::NOTIFY_COLOR_GRAY)
    {
        if (!$this->config->stringToBool($this->config->getEnv('NOTIFY_SLACK'))) {

            return false;
        }
        try {
            $this->shell->execForUser('/consul-conf/bin/notify-slack.sh', [$message, $color]);
        } catch (\Exception $e) {
            $this->sendLog('Failed to send Slack notification');
            $this->sendLog($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function sendLog($message)
    {
        return (bool)file_put_contents('/var/log/application.log', sprintf(
            "[%s] [uid=%s, ip=%s] %s\n",
            date('c'),
            $this->user->getUserId(),
            $this->user->getRealIp(),
            $message)
        );
    }

    /**
     * @param Shell $shell
     */
    public function setShell(Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}