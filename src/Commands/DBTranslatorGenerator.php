<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DBTranslatorGenerator extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dbtranslator:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if translations exists in file and generates a new version.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Get all files in views
         */
        $disk = Storage::disk('translator_views');
        $f = $disk->allFiles();
        foreach ($f as $file)
        {
            if (basename($file) != '.DS_Store')
            {
                $f = Storage::disk('translator_views')->get($file);
                preg_match_all("/{{ lang\((.*?)\) }}/", $f, $match);

                if (count($match[1]))
                {
                    foreach ($match[1] as $lang)
                    {
                        //lang('All rights reserved');

                        if (substr(trim($lang), 0, 1) != '$') {
                            $e = "lang(".$lang.");";
                            $e = eval($e);
                            $this->info('Added: '.$lang);
                        } else {
                            $this->warn('Cannot add dynamic variable to translations: '.$lang.' on file: '.$file);
                        }
                        
                    }
                }
                preg_match_all("/{{lang\((.*?)\)}}/", $f, $match);
                if (count($match[1]))
                {
                    foreach ($match[1] as $lang)
                    {
                        //lang('All rights reserved');
                        if (substr(trim($lang), 0, 1) != '\$') {
                            $e = "lang(".$lang.");";
                            $e = eval($e);
                            $this->info('Added: '.$lang);
                        } else {
                            $this->warn('Cannot add dynamic variable to translations: '.$lang.' on file: '.$file);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }
}
