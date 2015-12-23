<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use bernardomacedo\DBTranslator\Models\Intl;

class DBTranslatorRemove extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dbtranslator:remove';

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
        $variables = Intl::whereDynamic(0)->get()->toArray();
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
                            preg_match_all('/lang(?:\s*)\((?:(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\"))(?:(?:\s*)(?:\,(?:\s*)((?:\[|array\().*(?:\]|\))|null)(?:\s*)(?:\,(?:\s*)(?:(\d+?|null))(?:\s*)(?:\,(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\")(?:\s*)?)?)?)?)\)/', $match, $d);
                            if (!empty($d[1][0]) and (!empty($d[4][0]) and ($d[4][0] != 'null')))
                            {
                                if (!isset($check[$d[4][0]])) {
                                    $check[$d[4][0]] = [];
                                }
                                if (!in_array($d[1][0], array_values($check[$d[4][0]])))
                                {
                                    $check[$d[4][0]][] = $d[1][0];
                                }
                            } elseif (!empty($d[1][0])) {
                                
                                if (!isset($check['general'])) {
                                    $check['general'] = [];
                                }
                                if (!in_array($d[1][0], array_values($check['general'])))
                                {
                                    $check['general'][] = $d[1][0];
                                }
                            }
                        }
                    }
                    $f = preg_replace('/(lang(?:(\s?|\s+))\((?:(\s?|\s+))(?:(\\\'|\")|(\\$))([^\(\)]|(R))+(?:(\\\'|\")|(\]|null)|(\s?)|\s+)\))/', "''", $f);
                }
            }
        }
        /**
         * lets iterate though all variables in the database and see which ones need to be removed
         */
        if (count($variables) > 0) {
            $to_remove = false;
            foreach ($variables as $key => $variable) {
                if (!isset($check[$variable['group']]))
                {
                    $to_remove = true;
                    $remove[] = $variable['id'];
                    $this->info('TO REMOVE: "'.$variable['group'].':'.$variable['md5sha1'].'.'.$variable['text'].'"');
                } else {
                    if (!in_array($variable['text'], array_values($check[$variable['group']])))
                    {
                        $to_remove = true;
                        $remove[] = $variable['id'];
                        $this->info('TO REMOVE: "'.$variable['group'].':'.$variable['md5sha1'].'.'.$variable['text'].'"');
                    }
                }
            }
        }
        if ($to_remove) {
            if ($this->confirm('Do you wish to continue removing this from database? [yes|no]'))
            {
                foreach ($remove as $key => $id) {
                    $locale = Intl::find($id);
                    $locale->delete();
                    $this->info('REMOVING: '.$id);
                }
            }
            $this->info('Completed!');
        } else {
            $this->info('Nothing to remove!');
        }
        
    }
}
