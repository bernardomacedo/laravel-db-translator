# Laravel Database Translator

This package allows you to add translations to database.
It replaces the current language translator with a more efficient one.

## Install

``` bash
$ composer require bernardomacedo/laravel-db-translator:dev-master
```

First register the service provider and facade in your application.

```php
// config/app.php

'providers' => [
    ...
    bernardomacedo\DBTranslator\DBTranslatorServiceProvider::class,
];
'aliases' => [
    ...
    'DBTranslator' => bernardomacedo\DBTranslator\DBTranslatorFacade::class,
];
```

To publish all settings...

``` bash
php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider"
```

...or individually:

``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="config"
```
``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="migrations"
```
``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="lang"
```

And run migrations

``` bash
$ php artisan migrate
```

Add a disk to the `filesystems.php` filestorage:

If you change the default disk name, because it might conflict with another package or with a potential future one, be sure to change it under the published db-translator.php => storage_driver` parameter.

```
    'disks' => [
        ...
        'translator' => [
            'driver'    => 'local',
            'root'      => base_path('resources/lang/vendor/dbtranslator')
        ],
        ...
```

## Usage

In a blade template use:

```
{{ intl('some text to translate') }}
```

## Generating translations

on a controller class

```php
use bernardomacedo\DBTranslator\DBTranslator;

class SomeControllerName extends BaseController
{
    public function generate_translations()
    {
        /**
         * This will generate the language translations for all
         * translated texts in the database, and will assume
         * the original language by default.
         */
        DBTranslator::generate();
        /**
         * Redirect or do whatever you wish after generation
         */
        dd('completed');
    }
}
```
