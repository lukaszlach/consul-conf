<?php

namespace ConsulConf\Entity;

class Dashboard extends Base
{
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
    public function getColor()
    {
        return $this->get('color');
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
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return string
     */
    public function getVisibleDescription()
    {
        $result = \Parsedown::instance()
            ->setBreaksEnabled(true)
            ->setMarkupEscaped(false)
            ->parse($this->getDescription());

        return $result;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->get('path');
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
    public function isHidingUnconfigured()
    {
        return (bool)$this->get('hide_unconfigured');
    }
}