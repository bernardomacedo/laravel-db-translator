<?php

namespace bernardomacedo\DBTranslator\Models;

use Illuminate\Database\Eloquent\Model;

class Intl extends Model
{
    protected $table = config('db-translator.table_names.variables');

    public function translations()
    {
        return $this->hasMany('bernardomacedo\DBTranslator\Models\Translated');
    }
}
