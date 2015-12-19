<?php

use bernardomacedo\DBTranslator\Models\Intl;
use bernardomacedo\DBTranslator\Models\Languages;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

function language_name() {
    $lang = Languages::whereIso(Lang::getLocale())->firstOrFail();
    return $lang->name;
}

function lang($text = false)
{
    $args = func_get_args();
    unset($args[0]);
    foreach ($args as $key => $arg) {
        switch (gettype($arg))
        {
            case 'integer':
                $params['value'] = $arg;
                break;
            case 'array':
                $params['vars'] = $arg;
                break;
            case 'string':
                if (strlen($arg) == 2) {
                    $params['locale'] = $arg;
                } else {
                    $params['group'] = $arg;
                }
                break;
        }
    }

    if (preg_match('/^|/', $text)) {
        $choose = true;
    } else {
        $choose = false;
    }

    $config = config('db-translator');
    /**
     * lets save the locale in a variable
     */
    $original_locale        = Lang::getLocale();
    $file_namespace         = 'dbtranslator';
    $params['group']        = isset($params['group']) ? $params['group'] : 'general';
    $hash                   = md5($text).sha1($text);
    $params['vars']         = isset($params['vars']) ? $params['vars'] : [];
    $params['locale']       = isset($params['locale']) ? $params['locale'] : $original_locale;
    $params['value']        = isset($params['value']) ? $params['value'] : 0;
    /**
     * Before anything lets change the locale to the intl locale
     */
    App::setLocale($params['locale']);

    if (Lang::has($file_namespace.'::'.$params['group'].'.'.$hash))
    {
        if ($choose) {
            $text = trans_choice($file_namespace.'::'.$params['group'].'.'.$hash, $params['value'], $params['vars']);
            App::setLocale($original_locale);
            return $text;
        } else {
            $text = trans($file_namespace.'::'.$params['group'].'.'.$hash, $params['vars']);
            App::setLocale($original_locale);
            return $text;
        }
    }
    else
    {
        if (!Intl::whereText($text)->whereGroup($params['group'])->get()->count()) {
            $intl               = new Intl;
            $intl->text         = $text;
            $intl->group        = $params['group'];
            $intl->md5sha1      = $hash;
            $intl->save();
        }
        if ($choose) {
            $params['vars']['count'] = $params['value'];
            $ms = new MessageSelector;
            $text = $ms->choose($text, $params['value'], $params['locale']);
            $params['vars'] = (new Collection($params['vars']))->sortBy(function ($value, $key) {
                return mb_strlen($key) * -1;
            });
            foreach ($params['vars'] as $key => $value) {
                $text = str_replace(':'.$key, $value, $text);
            }
            App::setLocale($original_locale);
            return $text;
        } else {
            if (count($params['vars']))
            {
                $params['vars'] = (new Collection($params['vars']))->sortBy(function ($value, $key) {
                    return mb_strlen($key) * -1;
                });
                foreach ($params['vars'] as $key => $value) {
                    $text = str_replace(':'.$key, $value, $text);
                }
            }
            App::setLocale($original_locale);
            return $text;
        }
    }
}