<?php

namespace bernardomacedo\DBTranslator;

use bernardomacedo\DBTranslator\Models\Intl;
use bernardomacedo\DBTranslator\Models\Languages;
use bernardomacedo\DBTranslator\Models\Translated;
use Illuminate\Support\Facades\Storage;

class DBTranslator
{
    public function generate()
    {
        /**
         * First we get the list of languages and iterate for each one of them
         * to get the associated translations
         */
        $languages = Languages::whereStatus(1)->get();
        /**
         * Get the list of available variables in the database
         */
        $variables = Intl::all();
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
                $file = '<?php
return [';
                foreach ($trans as $hash => $translated)
                {
                    $save_value = str_replace("'", "\'", array_values($translated)[0]);
                    $file .= '\''.array_keys($translated)[0].'\' => \''.$save_value.'\',';
                }
                $file .= '];';
                \Storage::disk('translations')->put($language.'/'.$group.'.php', $file);
            }
        }
        dd('completed');
    }
}