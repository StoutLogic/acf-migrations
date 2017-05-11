<?php

use Codeception\Util\Stub;
use StoutLogic\ACF\Migrations\Field;
use StoutLogic\ACF\Migrations\HasMetaData;

class FieldCest
{
    public function getFieldNameFromSubject(UnitTester $I)
    {
        $I->wantTo('get the field name of a particular field');

        $field = new Field('field_position');
        $subject = Stub::constructEmpty(HasMetaData::class, [], [
            'getAllMetaData' => [
                'position' => 'President',
                '_position' => 'field_position',
            ],
        ]);

        $I->assertEquals('position', $field->getFieldNameFrom($subject));
    }
}
