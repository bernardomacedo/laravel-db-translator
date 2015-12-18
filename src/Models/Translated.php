<?php

namespace bernardomacedo\DBTranslator\Models;

use Illuminate\Database\Eloquent\Model;

class Translated extends Model
{
    protected $table = 'translations_translated';

    public function __construct()
    {
        parent::__construct();
        $this->table = config('db-translator.table_names.translated');
    }

    public function scopeLanguage($query, $lang)
    {
        return $query->whereLanguage_id($lang);
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
