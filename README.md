# Laravel Database Translator

[![Total Downloads](https://poser.pugx.org/bernardomacedo/laravel-db-translator/d/total.svg)](https://packagist.org/packages/bernardomacedo/laravel-db-translator)
[![Latest Stable Version](https://poser.pugx.org/bernardomacedo/laravel-db-translator/v/stable.svg)](https://packagist.org/packages/bernardomacedo/laravel-db-translator)
[![Latest Unstable Version](https://poser.pugx.org/bernardomacedo/laravel-db-translator/v/unstable.svg)](https://packagist.org/packages/bernardomacedo/laravel-db-translator)
[![License](https://poser.pugx.org/bernardomacedo/laravel-db-translator/license.svg)](https://packagist.org/packages/bernardomacedo/laravel-db-translator)

This package allows you to add translations to database.
It replaces the current language translator with a more efficient one.

## Compatibility
Laravel Framework 5.1.27

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


Default language set to `App::getLocale()`.

In a blade template use:
```php
{{ lang('some text to translate') }}
{{ lang(':count apple named :name|:count apples named :name', 2, ['name' => 'Bernardo']) }}
{{ lang('{0} There are no apples (:count) named :name|[1,19] There are some (:count) apples named :name|[20,Inf] There are many (:count) apples named :name', ['name' => 'Bernardo'], 2) }}
```

This translation method is easier to interpret because even if the translation is not found, the text you input will be returned.

If the translations exist and they are generated
```php
{{ lang('some text to translate') }} // returns 'algum texto para traduzir'
{{ lang('some text to translate', 'ru') }} // returns 'какой-нибудь текст' bypassing the current language forcing a locale.
{{ lang('this text does not exists on the database') }} // returns 'this text does not exists on the database' and will be added for future translation

## What groups are for?

Sometimes you need to generate a specific translation for a context based situation. Where the same phrase or text you wish to translate, means something different in other languages.
So, the group parameter allows you to differentiate the same translation to be translated differently depending on context.

```php
{{ lang('participations') }}                /* general group assumed */
{{ lang('participations', 'some_group') }}  /* some_group group assumed */
```

`Note: be sure the group parameter has more than 2 characters long, so DBTRanslator does not confuse it by a language.

As long as the translating text is the first function argument, you can place the other arguments in any order.

## Translating a text

```php
DBTranslator::doTranslation($variable_id, $text, $language_id, $group = 'general');
```

DBTranslator will try to find the `$variable_id` and the `$language_id` for you if you only supply strings.

```php
DBTranslator::doTranslation('This is cool', 'Isto é cool', 'pt');
```

Sometimes you wish to create a variable for translating, adding a translation directly on a language you choose.
So, supplying a string on $variable_id that does not exist on the `translations_variables` table, will generate a new one, adding an entry to the `translations_translated` table directly.

## Generating the translation files

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
         * This will work on all languages active by default.
         */
        DBTranslator::generate(); /* will generate all translations */

        DBTranslator::generate('pt'); /* will generate the Portuguese translations */
        DBTranslator::generate(64); /* will generate the Portuguese translations based on ID */
        /**
         * Redirect or do whatever you wish after generation
         */
        return redirect()->route('home');
    }
}
```

This will create all required files under the directory supplied on the `filesystems.php` file.

```
resources/lang/vendor/dbtranslator/
    - en
        - general.php
        - some_group.php
    - pt
        - general.php
        - some_group.php
```

## Getting all variables on database

```php
use bernardomacedo\DBTranslator\Models\Intl;

class SomeControllerName extends BaseController
{
    public function some_function()
    {
        $all = Intl::all();
        $group = Intl::group('general')->get();
    }
}
```

## Getting available translations

```php
use bernardomacedo\DBTranslator\Models\Translated;

class SomeControllerName extends BaseController
{
    public function some_function()
    {
        /**
         * Gets all available translations
         */
        $all = Translated::all();

        /**
         * Gets all available translations
         */
         $portuguese = Translated::language('pt')->get(); // using string ISO
         $portuguese = Translated::language(64)->get(); // using ID for the language
    }
}
```

### License

The Laravel Database Translator is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

