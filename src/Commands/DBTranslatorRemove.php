<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;
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
        /**
         * Get all files in views
         */
        $paths = [];
        
        $folders = config('view');
        $paths[] = base_path('app/Http');
        foreach ($folders['paths'] as $folder) {
            $paths[] = $folder;
        }
        
        $keys = [];
        $bar_path = $this->output->createProgressBar(count($paths));
        foreach ($paths as $key => $path) {
            //$this->info('PATH: "'.$path);
            $finder = new Finder();

            $bar_finder = $this->output->createProgressBar(count($finder->in($path)->name('*.php')->files()));
            foreach ($finder as $file) {
                $f = $file->getContents();
                preg_match_all("/lang(?:\\s*)\\((?:(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\"))(?:(?:\\s*)(?:\\,(?:\\s*)((?:\\[|array\\().*(?:\\]|\\))|null)(?:\\s*)(?:\\,(?:\\s*)(?:(\\d+?|null))(?:\\s*)(?:\\,(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\")(?:\\s*)?)?)?)?)\\)/", $f, $matches);
                if (count($matches[2]) > 0) {
                    foreach ($matches[2] as $k) {
                        while(preg_match_all("/lang(?:\\s*)\\((?:(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\"))(?:(?:\\s*)(?:\\,(?:\\s*)((?:\\[|array\\().*(?:\\]|\\))|null)(?:\\s*)(?:\\,(?:\\s*)(?:(\\d+?|null))(?:\\s*)(?:\\,(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\")(?:\\s*)?)?)?)?)\\)/", $k, $ka)) {
                            $keys[] = $ka[0][0];
                            $k = str_replace($ka[0][0], "null", $k);
                            $f = str_replace($ka[0][0], "null", $f);
                        }
                        while(preg_match_all("/lang(?:\\s*)\\((?:(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\"))(?:(?:\\s*)(?:\\,(?:\\s*)((?:\\[|array\\().*(?:\\]|\\))|null)(?:\\s*)(?:\\,(?:\\s*)(?:(\\d+?|null))(?:\\s*)(?:\\,(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\")(?:\\s*)?)?)?)?)\\)/", $f, $kv)) {
                            foreach ($kv[0] as $fa) {
                                $keys[] = $fa;
                                $f = str_replace($fa, "null", $f);
                            }
                        }
                    }
                }

                $bar_finder->advance();
            }
            $bar_path->advance();
        }
        
        $bar_finder->finish();
        $bar_path->finish();

        $keys = array_unique($keys);
        $check = [];
        $bar = $this->output->createProgressBar(count($keys));
        foreach ($keys as $key => $match) {
            if (! preg_match("/(lang(?:(\\s?|\\s+))\\()(?:(\\s?|\\s+))(?:(\\$))/", $match))
            {

                // if it is not a dynamic variable
                preg_match_all("/lang(?:\\s*)\\((?:(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\"))(?:(?:\\s*)(?:\\,(?:\\s*)((?:\\[|array\\().*(?:\\]|\\))|null)(?:\\s*)(?:\\,(?:\\s*)(?:(\\d+?|null))(?:\\s*)(?:\\,(?:\\s*)(?:\\'|\\\")(.*?)(?:\\'|\\\")(?:\\s*)?)?)?)?)\\)/", $match, $d);
                if (!empty($d[1][0]) and (!empty($d[4][0]) and ($d[4][0] != 'null')))
                {
                    $check[$d[4][0]][] = $d[1][0];
                    //$this->info('INSERTING: "'.$d[1][0].'" on "'.$d[4][0].'" group.');
                } elseif (!empty($d[1][0])) {
                    $check['general'][] = $d[1][0];
                    //$this->info('INSERTING: "'.$d[1][0].'" on "general" group.');
                }
            }
            $bar->advance();
        }
        $bar->finish();

        /**
         * lets iterate though all variables in the database and see which ones need to be removed
         */
        $toremove = false;
        if (count($variables) > 0) {
            foreach ($variables as $key => $variable) {
                if (!isset($check[$variable['group']]))
                {
                    $toremove = true;
                    $remove[] = $variable['id'];
                    $this->info('TO REMOVE: "'.$variable['group'].':'.$variable['md5sha1'].'.'.$variable['text'].'"');
                } else {
                    if (!in_array($variable['text'], array_values($check[$variable['group']])))
                    {
                        $toremove = true;
                        $remove[] = $variable['id'];
                        $this->info('TO REMOVE: "'.$variable['group'].':'.$variable['md5sha1'].'.'.$variable['text'].'"');
                    }
                }
            }
        }
        if ($toremove) {
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
