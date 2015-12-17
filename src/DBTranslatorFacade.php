<?php

namespace bernardomacedo\DBTranslator;

use Illuminate\Support\Facades\Facade;

class DBTranslatorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dbtranslator';
    }
}
