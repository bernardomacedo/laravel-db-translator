<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DBTranslatorAdd extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dbtranslator:add';

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
        $folders = config('view');
        foreach ($folders['paths'] as $folder) {
            $files = File::allFiles($folder);
            foreach ($files as $file) {
                $fa[] = $file;
            }
        }
        $check = [];
        foreach ($fa as $file)
        {
            $file = (string)$file;
            if (basename($file) != '.DS_Store')
            {
                $this->info('PROCESSING FILE: '.$file);
                $f = File::get($file);
                while(preg_match_all('/(lang(?:(\s?|\s+))\((?:(\s?|\s+))(?:(\\\'|\")|(\\$))([^\(\)]|(R))+(?:(\\\'|\")|(\]|null)|(\s?)|\s+)\))/', $f, $matches))
                {
                    foreach ($matches[0] as $key => $match) {
                        if (!preg_match('/(lang(?:(\s?|\s+))\()(?:(\s?|\s+))(?:(\\$))/', $match))
                        {
                            // if it is not a dynamic variable
                            preg_match_all('/lang(?:\s*)\((?:(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\"))(?:(?:\s*)(?:\,(?:\s*)((?:\[|array\().*(?:\]|\))|null)(?:\s*)(?:\,(?:\s*)(?:(\d+?|null))(?:\s*)(?:\,(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\")(?:\s*)?)?)?)?)\)/', $match, $d);
                            if (!empty($d[1][0]) and (!empty($d[4][0]) and ($d[4][0] != 'null')))
                            {
                                $insert = lang($d[1][0], null, null, $d[4][0], true);
                                $this->info('INSERTING: "'.$d[1][0].'" on "'.$d[4][0].'" group.');
                            } elseif (!empty($d[1][0])) {
                                $insert = lang($d[1][0], null, null, 'general', true);
                                $this->info('INSERTING: "'.$d[1][0].'" on "general" group.');
                            }
                        }
                    }
                    $f = preg_replace('/(lang(?:(\s?|\s+))\((?:(\s?|\s+))(?:(\\\'|\")|(\\$))([^\(\)]|(R))+(?:(\\\'|\")|(\]|null)|(\s?)|\s+)\))/', "''", $f);
                }
            }
        }
        $this->info('Completed!');
    }
}
