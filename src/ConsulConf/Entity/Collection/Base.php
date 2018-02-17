<?php

namespace ConsulConf\Entity\Collection;

abstract class Base
{
    /** @var array */
    protected $data = [];
    /** @var string|null */
    protected $classMap;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->initialize();
    }

    protected function initialize()
    {
    }

    /**
     * @param mixed $record
     */
    public function add($record)
    {
        $this->data[] = $record;
    }

    /**
     * @param string $id
     * @param mixed $record
     */
    public function set($id, $record)
    {
        $this->data[$id] = $record;
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    private function get($id)
    {
        return $this->data[$id] ?? null;
    }

    /**
     * @return mixed
     */
    public function getOne()
    {
        return $this->getAll()->current();
    }

    /**
     * @return \Generator
     */
    public function getAll()
    {
        foreach ($this->data as $key => $record) {
            if (null !== $this->classMap) {
                $record = $this->classMap($record, $key);
            }
            yield $record;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param string $className
     */
    public function setClassMap($className)
    {
        $this->classMap = $className;
    }

    /**
     * @param mixed $record
     * @param string $key
     * @return object
     */
    protected function classMap($record, $key)
    {
        return new $this->classMap($record);
    }
}