<?php

namespace StoutLogic\ACF\Migrations;

class Comment extends MetaDataType
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'comment';
    }
}
