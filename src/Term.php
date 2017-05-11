<?php

namespace StoutLogic\ACF\Migrations;

class Term extends MetaDataType
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'term';
    }
}
