<?php

namespace ConsulConf\Service;

class Config
{
    /**
     * @return string|null
     */
    public function getCryptKey()
    {
        return $this->getEnv('CRYPT_KEY') ?: null;
    }

    /**
     * @return bool
     */
    public function isLdapEnabled()
    {
        return $this->stringToBool($this->getEnv('LDAP_ENABLED'));
    }

    /**
     * @return bool
     */
    public function getBasicAuth()
    {
        return $this->getEnv('BASIC_AUTH') ?: null;
    }

    /**
     * @return bool
     */
    public function isBasicAuthEnabled()
    {
        return !empty($this->getEnv('BASIC_AUTH'));
    }

    /**
     * @param string $variable
     * @return mixed
     */
    public function getEnv($variable)
    {
        return getenv($variable);
    }

    /**
     * @param string $string
     * @return bool
     */
    public function stringToBool($string)
    {
        return ('1' === (string)$string || 'true' === (string)$string);
    }
}