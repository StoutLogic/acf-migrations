<?php

namespace StoutLogic\ACF\Migrations;

/**
 * Class ChangeKeyTransform
 * @package StoutLogic\ACF\Migrations
 *
 * Changes the key of an ACF field, maintaining the field name and field value
 */
class ChangeKeyTransform extends Transform
{
    /**
     * @var string
     */
    private $newKey;

    /**
     * @param string $newKey
     */
    public function __construct($newKey)
    {
        $this->newKey = $newKey;
    }

    public function transformSubject(HasMetaData $subject)
    {
        $fieldName = $this->getField()->getFieldNameFrom($subject);
        $subject->updateMetaData("_$fieldName", $this->newKey);
    }

    public function transformField()
    {
        $this->getField()->setKey($this->newKey);
    }
}
