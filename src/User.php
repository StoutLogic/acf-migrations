<?php

namespace StoutLogic\ACF\Migrations;

class User extends MetaDataType
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'user';
    }
}
