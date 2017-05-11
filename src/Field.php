<?php

namespace StoutLogic\ACF\Migrations;

class Field
{

    private $key;

    /**
     * Field constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $key
     * @return Field
     */
    public function setKey(string $key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    public function getFieldNameFrom(HasMetaData $subject)
    {
        $metaData = $subject->getAllMetaData();

        $fieldName = array_merge(
            array_keys($metaData, [$this->getKey()]),
            array_keys($metaData, $this->getKey())
        );

        if (is_array($fieldName)) {
            $fieldName = array_shift($fieldName);
        }
        return ltrim($fieldName, '_');
    }


}