<?php

use Codeception\Util\Stub;
use StoutLogic\ACF\Migrations\ChangeKeyTransform;
use StoutLogic\ACF\Migrations\ChangeNameTransform;
use StoutLogic\ACF\Migrations\Field;
use StoutLogic\ACF\Migrations\HasMetaData;

class ChangeNameTransformCest
{
    public function transform(UnitTester $I)
    {
        $I->wantTo('Change they name of a field from "position" to "title"');

        $metaData =  [
            'position' => ['President'],
            '_position' => ['field_position'],
        ];

        $field = new Field('field_position');
        $post = Stub::constructEmpty(HasMetaData::class, [], [
            'updateMetaData' => Stub::exactly(2),
            'updateMetaData' => Stub::exactly(2),
            'getAllMetaData' => $metaData,
            'getMetaData' => function($key) use ($metaData){
                return $metaData[$key];
            }
        ]);

        $transform = new ChangeNameTransform('title');
        $transform->setField($field);
        $transform->transformSubject($post);
        $transform->transformField();

        $I->assertEquals('field_position', $field->getKey());
    }
}
