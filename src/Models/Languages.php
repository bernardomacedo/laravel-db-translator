<?php

namespace bernardomacedo\DBTranslator\Models;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    protected $table = config('db-translator.table_names.languages');

    public function users()
    {
        return $this->hasMany(config('db-translator.models.user'), config('db-translator.user_language_column'), 'iso');
    }
}