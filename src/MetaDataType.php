<?php

namespace StoutLogic\ACF\Migrations;

abstract class MetaDataType implements HasMetaData
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public abstract function getType();

    public function getMetaData($key = '')
    {
        return get_metadata($this->getType(), $this->id, $key);
    }

    public function getAllMetaData()
    {
        return $this->getMetaData();
    }

    public function updateMetaData($key, $value, $oldValue = '')
    {
        return update_metadata($this->getType(), $this->id, $key, $value, $oldValue);
    }

    public function deleteMetaData($key)
    {
        return delete_metadata($this->getType(), $this->id, $key);
    }
}