<?php

namespace bernardomacedo\DBTranslator;

use bernardomacedo\DBTranslator\Models\Intl;
use bernardomacedo\DBTranslator\Models\Languages;
use bernardomacedo\DBTranslator\Models\Translated;
use Illuminate\Support\Facades\Storage;

class DBTranslator
{

    public static function hasTranslation($variable_id, $language_id)
    {
        return Translated::VarLang($variable_id, $language_id)->first();
    }

    /**
     * Saves a translation into database.
     * 
     * @param int $variable_id, variable_id 
     * @param int $text 
     * @param int $language_id, string 
     * @param string $group
     * 
     * @return bool
     */
    public static function doTranslation($variable_id, $text, $language_id, $group = 'general')
    {
        if (! is_integer($language_id)) {
            $language_id = Languages::whereIso($language_id)->first()->id;
        }
        if (! is_integer($variable_id)) {
            /**
             * First lets check if the translation exists or it has been previously
             * submitted for translation
             */
            if ($var = Intl::whereText($variable_id)->whereGroup($group)->first()) {
                $variable_id = $var->id;
            } else {
                $var = new Intl;
                $var->text = $variable_id;
                $var->group = $group;
                $var->md5sha1 = md5($text).sha1($text);
                $var->save();
                $variable_id = $var->id;
            }
        }

        $translated = Translated::VarLang($variable_id, $language_id);
        $translated->delete();

        $trans = new Translated;
        $trans->variable_id = $variable_id;
        $trans->language_id = $language_id;
        $trans->translation = $text;
        $trans->save();
        return true;
    }

    public static function generate($language = false)
    {
        $config = config('db-translator');
        /**
         * First we get the list of languages and iterate for each one of them
         * to get the associated translations
         */
        if ($language)
        {
            if (is_integer($language))
            {
                $languages[] = Languages::find($language);
            } else {
                $languages = Languages::whereIso($language)->get();
            }
        } else {
            $languages = Languages::whereStatus(1)->get();
        }
        /**
         * Get the list of available variables in the database
         */
        $variables = Intl::all();
        $arr = [];
        foreach ($languages as $language)
        {
            /**
             * Lets generate a list of variables per language and
             * all translations associated
             */
            foreach ($variables as $variable)
            {
                /**
                 * Generate per group
                 */
                $trans = Translated::VarLang($variable->id, $language->id)->first()['translation'];
                $translation = ($trans) ? $trans : $variable->text;
                $arr[$language->iso][$variable->group][] = [md5($variable->text).sha1($variable->text) => $translation ];
            }
        }
        
        foreach ($arr as $language => $values)
        {
            foreach ($values as $group => $trans)
            {
                $file = "<?php
return [\r\n";
                foreach ($trans as $hash => $translated)
                {
                    $save_value = str_replace('"', '\"', array_values($translated)[0]);
                    $file .= "    \"".array_keys($translated)[0]."\" => \"".$save_value."\",\r\n";
                }
                $file .= "];";
                \Storage::disk($config['storage_driver'])->put($language.'/'.$group.'.php', $file);
            }
        }
        return true;
    }
}