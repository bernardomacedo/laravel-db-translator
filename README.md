# Laravel Database Translator

This package allows you to add translations to database.
It replaces the current language translator with a more efficient one.

## Install

``` bash
$ composer require bernardomacedo/laravel-db-translator
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

Then register the macros in `App\Providers\RouteServiceProvider::boot()`.

```php
// app/Providers/RouteServiceProvider.php

use DBTranslator;

// ...

public function boot(Router $router)
{
    DBTranslator::registerMacros();
    
    parent::boot($router);
}
```

Then add an entry to the filesystems config file in `config/filesystems.php`.
This is where your generated translation files will go.

```php
    'disks' => [
        ...
        'translations' => [
            'driver'    => 'local',
            'root'      => base_path('resources/vendor/dbtranslator'),
        ],
        ...
    ]
```

To create the migrations use:

``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="config"
```
``` bash
$ php artisan vendor:publish --provider="bernardomacedo\DBTranslator\DBTranslatorServiceProvider" --tag="migrations"
```

## Usage

In a blade template use:

```
{{ intl('some text to translate') }}
```


