<?php

use Codeception\Util\Stub;
use StoutLogic\ACF\Migrations\ChangeKeyTransform;
use StoutLogic\ACF\Migrations\Field;
use StoutLogic\ACF\Migrations\HasMetaData;

class ChangeKeyTransformCest
{
    public function transform(UnitTester $I)
    {
        $I->wantTo('Change they key of a field from "field_position" to "field_title"');

        $field = new Field('field_position');
        $post = Stub::constructEmpty(HasMetaData::class, [], [
            'updateMetaData' => Stub::exactly(1, function($key, $value) use ($I) {
                $I->assertEquals('_position', $key);
                $I->assertEquals('field_title', $value);
            }),
            'getAllMetaData' => [
                'position' => ['President'],
                '_position' => ['field_position'],
            ]
        ]);

        $transform = new ChangeKeyTransform('field_title');
        $transform->setField($field);
        $transform->transformSubject($post);
        $transform->transformField();

        $I->assertEquals('field_title', $field->getKey());
    }
}
