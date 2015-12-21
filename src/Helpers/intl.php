<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use bernardomacedo\DBTranslator\Models\Intl;
use bernardomacedo\DBTranslator\Models\Languages;

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
    /**
     * This will check the existence of the translation on the locale given, not the default
     */
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
        /**
         * If we are using the database translations
         */
        if ($config['use_database'])
        {
            if (!Intl::whereText($text)->whereGroup($params['group'])->get()->count()) {
                $intl               = new Intl;
                $intl->text         = $text;
                $intl->group        = $params['group'];
                $intl->md5sha1      = $hash;
                $intl->save();
            }
        } else {
            /**
             * first we will check if the translation exists under the fallback language
             */
            App::setLocale($config['default_locale']);
            if (!Lang::has($file_namespace.'::'.$params['group'].'.'.$hash))
            {
                // if we don't find it, we should include the file for the original locale
                // change it with the new array, and save it.
                /**
                 * Determine if the file exists
                 */
                if (Storage::disk($config['storage_driver'])->exists($config['default_locale'].'/'.$params['group'].'.php'))
                {
                    $lang_array = require base_path('resources/lang/vendor/dbtranslator/'.$config['default_locale'].'/'.$params['group'].'.php');
                    if (!isset($lang_array[$hash]))
                    {
                        $lang_array[$hash] = $text;
                        $file = "<?php
    return [\r\n";
                        foreach ($lang_array as $k => $v)
                        {
                            $file .= "    \"".$k."\" => \"".str_replace('"', '\"', $v)."\",\r\n";
                        }
                        $file .= "];";
                        Storage::disk($config['storage_driver'])->put($config['default_locale'].'/'.$params['group'].'.php', $file);
                    }
                    
                } else {
                    $file = "<?php
return [\r\n";
                    $file .= "    \"".$hash."\" => \"".str_replace('"', '\"', $text)."\",\r\n";
                    $file .= "];";
                    Storage::disk($config['storage_driver'])->put($config['default_locale'].'/'.$params['group'].'.php', $file);
                }
            }
            App::setLocale($params['locale']);
        }
        // now we will process the string
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