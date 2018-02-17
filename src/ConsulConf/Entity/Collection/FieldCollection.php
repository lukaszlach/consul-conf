<?php

namespace ConsulConf\Entity\Collection;

use ConsulConf\Entity\Field;
use ConsulConf\Service\Dashboard;

class FieldCollection extends Base
{
    /** @var string|null */
    protected $basePath;

    /**
     * @param string $key
     * @return string
     */
    public function getValue($key)
    {
        $field = $this->getField($key);

        return ($field ? $field->getValue() : null);
    }

    /**
     * @param string $key
     * @return Field|null
     */
    public function getField($key)
    {
        $fullKey       = $key;
        $relativeKey   = preg_replace('#^'.preg_quote($this->basePath, '#').'/#', '', $fullKey);
        $fullConfigKey = $this->basePath.'/'.Dashboard::CONSUL_PATH_DASHBOARD."/$relativeKey";
        if (null !== $this->basePath && 0 !== strpos($key, $this->basePath)) {
            $fullKey = "{$this->basePath}/$key";
            $fullConfigKey = "{$this->basePath}/".Dashboard::CONSUL_PATH_DASHBOARD."/$key";
        }
        if (!isset($this->data[$fullKey])) {

            return null;
        }
        $hasConfig = isset($this->data[$fullConfigKey]);
        $config    = $hasConfig ? (array)json_decode($this->data[$fullConfigKey], true) : [];
        $value = $this->data[$fullKey];
        $field = new Field(array_merge(
            $config,
            [
                'key'           => $fullKey,
                'relative_key'  => $relativeKey,
                'value'         => $value,
                'has_config'    => $hasConfig,
            ]
        ));

        return $field;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param mixed $record
     * @param string $key
     * @return Field
     */
    protected function classMap($record, $key)
    {
        return $this->getField($key);
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->setClassMap(Field::class);
        parent::initialize();
    }
}