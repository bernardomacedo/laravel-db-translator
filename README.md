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
function lang($text = false, $vars = null, $value = null, $group = null, $locale = null)
```

```php
{{ lang('some text to translate') }}
{{ lang(':count apple named :name|:count apples named :name', ['name' => 'Bernardo'], 2) }}
{{ lang('{0} There are no apples (:count) named :name|[1,19] There are some (:count) apples named :name|[20,Inf] There are many (:count) apples named :name', ['name' => 'Bernardo'], 2) }}
```

This translation method is easier to interpret because even if the translation is not found, the text you input will be returned.

If the translations exist and they are generated

```php
{{ lang('some text to translate') }} // returns 'algum texto para traduzir'
{{ lang('some text to translate', null, null, null, 'ru') }} // returns 'какой-нибудь текст' bypassing the current language forcing a locale.
{{ lang('this text does not exists on the database') }} // returns 'this text does not exists on the database' and will be added for future translation

## What groups are for?

Sometimes you need to generate a specific translation for a context based situation. Where the same phrase or text you wish to translate, means something different in other languages.
So, the group parameter allows you to differentiate the same translation to be translated differently depending on context.

```php
{{ lang('participations') }}                /* general group assumed */
{{ lang('participations', null, null, 'some_group') }}  /* some_group group assumed */
```

### Dynamic groups and variables
When using dynamic variables for translation, be sure to force a group named 'dynamic_...'

```php
{{ lang($language_name, null, null, 'dynamic_language') }} /* language group assumed with dynamic flag on database */
{{ lang($SomeDynamicVar, null, null, 'dynamic_some_group') }}      /* some_group group assumed with dynamic flag on database */
```


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

## Inserting / Removing translations into/from database without rendering views in the browser

You can add translations to database without rendering a view.
For this you can run a artisan command and `Laravel Database Translator` will check your view folders configured under `config/view.php` config file `paths` and will add any element of lang(*) found to the database.

### Inserting translations

``` bash
$ php artisan dbtranslator:add
```

### Removing unused translations

``` bash
$ php artisan dbtranslator:remove
```

When running these commands, `Dynamic groups` and `$variables will be ignored.
eg:

```php
lang($php_var) /* is not supported so they will be ignored */
```

## Generating translations

Generates for Portuguese
``` bash
$ php artisan dbtranslator:generate pt
```
Generates for Spanish
``` bash
$ php artisan dbtranslator:generate es
```
Generates all languages
``` bash
$ php artisan dbtranslator:generate --status=all
```
Generates active languages
``` bash
$ php artisan dbtranslator:generate --status=active
```
Generates inactive languages
``` bash
$ php artisan dbtranslator:generate --status=inactive
```

## License

The Laravel Database Translator is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

