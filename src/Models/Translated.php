<?php

namespace bernardomacedo\DBTranslator\Models;

use Illuminate\Database\Eloquent\Model;
use bernardomacedo\DBTranslator\Models\Languages;

class Translated extends Model
{
    protected $table = 'translations_translated';

    public function __construct()
    {
        parent::__construct();
        $this->table = config('db-translator.table_names.translated');
    }

    public function scopeLanguage($query, $language_id)
    {
        if (!is_integer($language_id))
        {
            /**
             * Lets find the language id for this 
             */
            $language_id = Languages::whereIso($language_id)->first()->id;
        }
        return $query->whereLanguage_id($language_id);
    }

    public function scopeVariable($query, $var)
    {
        return $query->whereVariable_id($var);
    }

    public function scopeVarLang($query, $var, $lang)
    {
        return $query->whereVariable_id($var)->whereLanguage_id($lang);
    }
}
