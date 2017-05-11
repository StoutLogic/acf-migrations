<?php

namespace StoutLogic\ACF\Migrations;

abstract class Transform
{
    /**
     * @var Field
     */
    private $field;

    abstract public function transformSubject(HasMetaData $subject);

    abstract public function transformField();

    /**
     * @param Field $field
     * @return $this
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }
}
