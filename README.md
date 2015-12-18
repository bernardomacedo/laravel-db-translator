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

To create the migrations use:

``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="config"
```
``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="migrations"
```
``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="lang"
```

## Usage

In a blade template use:

```
{{ intl('some text to translate') }}
```


