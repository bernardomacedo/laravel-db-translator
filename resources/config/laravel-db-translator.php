<?php

return [
    'table_names' => [
        /**
         * General tables required for DBTranslator to work
         */
        'variables' => 'translations_variables',
        'translated'=> 'translations_translated',
        'languages' => 'languages',
        /**
         * Place your users table in this option
         */
        'users'     => 'users'
    ],
    'columns' => [
        /**
         * Place your users table language column in this option
         */
        'user_language_column' => 'language'
    ],
    'models' => [
        /**
         * Place your User model in this option
         */
        'user'  => config('auth.model'),
    ],
    /**
     * If your driver name is different than the default one, please add it here
     */
    'storage_driver' => 'translator'
];