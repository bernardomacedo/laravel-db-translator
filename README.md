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

Add a filesystem.php storage:

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


