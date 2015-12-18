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

function intl($text = false, $params = null)
{
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
    $params['explanation']  = isset($params['explanation']) ? $params['explanation'] : null;
    /**
     * Before anything lets change the locale to the intl locale
     */
    App::setLocale($params['locale']);

    if (Lang::has($file_namespace.'::'.$params['group'].'.'.$hash))
    {
        if (isset($params['choice'])) {
            if (!isset($params['value'])) {
                throw new Exception('To choose translation, you need to set a "value" parameter');
            }
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
        /**
         * CHECK THE CHOICE METHOD SO IT CAN CHOOSE FROM THE TRANSLATED SECTION
         */

        /**
         * If there is no translation saved, let's save it to the database
         * but only if it does not find anything
         */
        if (!Intl::whereText($text)->whereGroup($params['group'])->get()->count()) {
            $intl               = new Intl;
            $intl->text         = $text;
            $intl->group        = $params['group'];
            $intl->explanation  = $params['explanation'];
            $intl->md5sha1      = $hash;
            $intl->save();
        }

        if (isset($params['choice']))
        {
            if (!isset($params['value']))
            {
                throw new Exception('To choose translation, you need to set a "value" parameter');
            }
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