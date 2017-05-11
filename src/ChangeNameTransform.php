<?php

namespace StoutLogic\ACF\Migrations;

/**
 * Class ChangeNameTransform
 * @package StoutLogic\ACF\Migrations
 *
 * Changes the name of an ACF field, maintaining the field key and field value
 */
class ChangeNameTransform extends Transform
{
    /**
     * @var string
     */
    private $newName;

    /**
     * @param string $newName
     */
    public function __construct($newName)
    {
        $this->newName = $newName;
    }

    public function transformSubject(HasMetaData $subject)
    {
        $oldFieldName = $this->getField()->getFieldNameFrom($subject);

        $this->updateFieldName($subject, $oldFieldName);
        $this->deleteOldFieldName($subject, $oldFieldName);
    }

    public function transformField()
    {

    }

    /**
     * @param HasMetaData $subject
     * @param string $oldFieldName
     */
    private function updateFieldName(HasMetaData $subject, $oldFieldName)
    {
        $fieldValue = $subject->getMetaData($oldFieldName);
        if (is_array($fieldValue)) {
            $fieldValue = $fieldValue[0];
        }

        $subject->updateMetaData($this->newName, $fieldValue);
        $subject->updateMetaData("_$this->newName", $this->getField()->getKey());
    }

    /**
     * @param HasMetaData $subject
     * @param string $oldFieldName
     */
    private function deleteOldFieldName(HasMetaData $subject, $oldFieldName)
    {
        $subject->deleteMetaData($oldFieldName);
        $subject->deleteMetaData("_$oldFieldName");
    }
}
