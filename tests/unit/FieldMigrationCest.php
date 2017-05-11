<?php

use Codeception\Util\Stub;
use StoutLogic\ACF\Migrations\FieldMigration;
use StoutLogic\ACF\Migrations\HasMetaData;
use StoutLogic\ACF\Migrations\OptionPage;
use StoutLogic\ACF\Migrations\Transform;

class FieldMigrationCest
{
    public function changeName(UnitTester $I)
    {
        $I->wantTo('be able to change the name of the field "position" to "title"');
        $fieldMigration = new FieldMigration('field_position');
        $return = $fieldMigration->changeName('title');

        $I->expect($return instanceof $fieldMigration);
    }

    public function changeKey(UnitTester $I)
    {
        $I->wantTo('be able to change the key from "field_position" to "field_title"');
        $fieldMigration = new FieldMigration('field_position');
        $return = $fieldMigration->changeKey('field_title');

        $I->expect($return instanceof $fieldMigration);
    }

    public function addTransform(UnitTester $I)
    {
        $I->wantTo('add a transform to a field migration');

        $transform = Stub::constructEmpty(Transform::class);

        $fieldMigration = new FieldMigration('field_key');
        $fieldMigration->addTransform($transform);

        $I->assertCount(1, $fieldMigration->getTransforms());
    }

    public function migrate(UnitTester $I)
    {
        $I->wantTo('perform a migration');

        $subjects = array_map(function () {
            return Stub::makeEmpty(HasMetaData::class);
        }, range(1, 10));

        $fieldMigration = Stub::construct(FieldMigration::class, ['field_postion'], [
            'getSubjects' => $subjects,
        ]);

        $transform = Stub::construct(Transform::class, [], [
            'transformSubject' => Stub::exactly(10),
            'transformField' => Stub::once(),
        ]);

        $fieldMigration
            ->addTransform($transform)
            ->migrate();
    }

    public function includeOptionPage(UnitTester $I)
    {
        $I->wantTo('specify specific option pages to include other than the defaults');

        $fieldMigration = new FieldMigration('field_key');
        $fieldMigration
            ->includeOptionPage('header')
            ->includeOptionPage('footer');

        $I->assertContains('header', $fieldMigration->getOptionPages());
        $I->assertContains('footer', $fieldMigration->getOptionPages());
    }

    public function excludeOptionPage(UnitTester $I)
    {
        $I->wantTo('specify specific option pages to exclude, such as the defaults');

        $fieldMigration = new FieldMigration('field_key');
        $fieldMigration
            ->includeOptionPage('header')
            ->excludeOptionPage('option');

        $I->assertContains('header', $fieldMigration->getOptionPages());
        $I->assertContains('options', $fieldMigration->getOptionPages());
        $I->assertNotContains('option', $fieldMigration->getOptionPages());
    }
}

