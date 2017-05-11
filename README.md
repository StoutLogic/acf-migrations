# ACF Migrations

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/StoutLogic/acf-migrations/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/StoutLogic/acf-migrations/?branch=master)

___Warning: Not for production use yet.___ 

Allow yourself to refactor [Advanced Custom Fields](https://www.advancedcustomfields.com/) field names and field keys without losing content in existing fields.

Don't you hate when you rename a field you lose all associated content with that field? It really hurts any refactoring efforts, and really forces you to get it right the first time.

The ACF Migrations library gives you tools to change field names and keys, and then update the database for you.

The ACF Migrations code you write can exist inside a single use script, or a custom theme or plugin upgrade script you maintain or even inside full blown migration library (to come).

**NOTE:** This does not change the name of the field inside ACF. That is either done via the UI, in the json file or in your own PHP code. After (or before) that, migrate your existing content to use the new field name and or field key.

### Simple Example
A custom `location` post type has a `zip_code` field. Due to expansion outside of the US and into Canada, we want to rename the field to be `postal_code`. Of course we can just change the label on the field to reflect this change to the admin user, we also want to change it in our code so that there is no confusion amoung developers.

The field's name is `zip_code` and the key is `field_zip_code`. And we want to change the name to `postal_code` and the key to `field_postal_code`.
```php
$migrateField = new FieldMigration('field_zip_code');
$migrateField
    ->changeName('postal_code')
    ->changeKey('field_postal_code')
    ->migrate();
```
Once `migrate` is called, all instances of meta data with the field key 'field_zip_code' on Posts, Terms, Users, Comments and Options Pages will be changed.

## Install
Use composer to install into your composer based WordPress theme or plugin:
```
composer require stoutlogic/acf-migrations
```

## Tests
To run the tests you can manually run. The integration tests will require local configuration. Instructions to come.
```
composer run tests
```

## Requirements
PHP 5.6 and later are supported.
