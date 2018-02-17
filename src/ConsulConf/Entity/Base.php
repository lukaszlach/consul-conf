<?php

namespace ConsulConf\Entity;

abstract class Base
{
    /** @var mixed */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->assign($data);
        $this->initialize();
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return \Generator
     */
    public function getAll()
    {
        reset($this->data);
        while ($key = key($this->data)) {
            $value = $this->get($key);
            yield $value;
        }
    }

    /**
     *
     */
    protected function initialize()
    {
    }

    /**
     * @param mixed $data
     */
    protected function assign($data)
    {
        $this->data = $data;
    }
}