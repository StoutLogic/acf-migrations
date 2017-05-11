<?php

namespace StoutLogic\ACF\Migrations;

interface HasMetaData
{
    public function __construct($id);

    /**
     * @param string $key
     * @return array|false
     */
    public function getMetaData($key);

    /**
     * @return array|false
     */
    public function getAllMetaData();

    /**
     * @param string $key
     * @param string $value
     * @param string $oldValue
     * @return false|int
     */
    public function updateMetaData($key, $value, $oldValue = '');

    /**
     * @param string $key
     * @return bool
     */
    public function deleteMetaData($key);
}