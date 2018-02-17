<?php

namespace ConsulConf\Entity;

use ConsulConf\Service\Dashboard as DashboardService;

class Field extends Base
{
    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->getKey();
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return string
     */
    public function getVisibleDescription()
    {
        return \Parsedown::instance()
            ->setBreaksEnabled(true)
            ->setMarkupEscaped(true)
            ->line($this->getDescription());
    }

    /**
     * @return string|null
     */
    public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->get('color');
    }

    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->get('key');
    }

    /**
     * @return string
     */
    public function getVisibleKey()
    {
        return $this->get('relative_key') ?: $this->getKey();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->get('value');

        return $value;
    }

    /**
     * @return string|null
     */
    public function getDefault()
    {
        return $this->get('default');
    }

    /**
     * @return bool
     */
    public function isInternal()
    {
        return false !== strpos($this->getKey(), '/'. DashboardService::CONSUL_PATH_DASHBOARD.'/');
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return (bool)$this->get('readonly');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return (bool)$this->get('hidden');
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return !$this->isHidden();
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return (bool)$this->get('has_config');
    }
}