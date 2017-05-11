<?php

namespace StoutLogic\ACF\Migrations;

class Post extends MetaDataType
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'post';
    }
}