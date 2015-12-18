<?php

namespace bernardomacedo\DBTranslator\Models;

use Illuminate\Database\Eloquent\Model;

class Intl extends Model
{
    protected $table = 'translations_variables';

    public function translations()
    {
        $this->table = config('db-translator.table_names.variables');
        return $this->hasMany('bernardomacedo\DBTranslator\Models\Translated');
    }
}
