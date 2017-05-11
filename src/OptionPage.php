<?php

namespace StoutLogic\ACF\Migrations;

class OptionPage implements HasMetaData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    public function getMetaData($key)
    {
        return get_option($this->getOptionPageKey($key));
    }

    public function getAllMetaData()
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT option_name, option_value FROM $wpdb->options WHERE option_name REGEXP '%s'",
            "_?{$this->getId()}_.+"
        );
        $results = $wpdb->get_results($query, OBJECT);

        $options = [];
        foreach ($results as $o) {
            $options[$this->stripOptionId($o->option_name)] = $o->option_value;
        }

        return $options;
    }

    /**
     * @param $metaValue
     * @return bool
     */
    public function hasMetaValue($metaValue)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT option_name, option_value FROM $wpdb->options WHERE option_name REGEXP '%s' and option_value = '%s'",
            "_?{$this->getId()}_.+", $metaValue
        );
        $results = $wpdb->get_results($query);

        return $results !== [];
    }

    public function updateMetaData($fieldName, $value, $oldValue = '')
    {
        return update_option($this->getOptionPageKey($fieldName), $value);
    }

    public function deleteMetaData($fieldName)
    {
        return delete_option($this->getOptionPageKey($fieldName));
    }

    /**
     * @param string $key
     * @return string
     */
    public function getOptionPageKey($key)
    {
        $pre = '';
        if ($key[0] === '_') {
            $pre = '_';
            $key = substr($key, 1);
        }
        if ($this->getId()) {
            return $pre . $this->getId() . '_' . $key;
        }

        return $pre . $key;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function stripOptionId($fieldName)
    {
        return str_replace($this->getId(), '', $fieldName);
    }
}