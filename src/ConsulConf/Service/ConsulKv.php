<?php

namespace ConsulConf\Service;

class ConsulKv
{
    /** @var Shell */
    protected $shell;
    /** @var Notify */
    protected $notify;

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $result = $this->shell->execForUser('/consul-conf/bin/consul-get.sh', [$key]);
        $result = $this->decodeBase64($result);

        return $result;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getJson($key)
    {
        $result = $this->get($key);
        $result = $this->jsonDecode($result);

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $jsonEncode
     */
    public function set($key, $value, $jsonEncode = true)
    {
        if ($jsonEncode) {
            $value = json_encode($value);
        }
        $this->shell->execForUser('/consul-conf/bin/consul-set.sh', [$key, $value]);
        $this->notify->send("Stored $key=$value");
    }

    /**
     * @return array
     */
    public function findAllDashboardPaths()
    {
        $result = $this->shell->execForUser('/consul-conf/bin/consul-get-all-dashboard-keys.sh');
        $result = $this->jsonDecode($result);

        return $result;
    }

    /**
     * @param string $basePath
     * @return array
     */
    public function findAllKeys($basePath = '')
    {
        $result = $this->shell->execForUser('/consul-conf/bin/consul-get-all-keys.sh', [$basePath]);
        $result = $this->jsonDecode($result);

        return $result;
    }

    /**
     * @param string $basePath
     * @return array
     */
    public function getAllValues($basePath = '')
    {
        $result = $this->shell->execForUser('/consul-conf/bin/consul-get-all-values.sh', [$basePath]);
        $result = $this->jsonDecode($result);
        $result = $this->decodeBase64Array($result);

        return $result;
    }

    /**
     * @return bool
     */
    public function isReachable()
    {
        try {
            $this->shell->execForUser('/consul-conf/bin/consul-is-reachable.sh');
        } catch (\Exception $e) {

            return false;
        }

        return true;
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
     * @param string $string
     * @return mixed
     */
    public function jsonDecode($string)
    {
        return json_decode($string, true);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function decodeBase64($string)
    {
        return base64_decode($string);
    }

    /**
     * @param array $array
     * @return array
     */
    protected function decodeBase64Array(array $array)
    {
        return array_map('base64_decode', $array);
    }
}