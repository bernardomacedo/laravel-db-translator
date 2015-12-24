<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

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

                //$this->info('FILE: "'.$file->getRelativePathname());
                $f = $file->getContents();
                while(preg_match_all("/(lang\\s*\\(\\s*(?:\\'|\\\"|\\$)(?:[^\\(\\)]|(R))+(?:\\'|\\\"|\\]|null|\\s*)\\))/", $f, $matches)) {
                    // Get all matches
                    foreach ($matches[0] as $key) {
                        $keys[] = $key;
                    }
                    $f = preg_replace("/(lang\\s*\\(\\s*(?:\\'|\\\"|\\$)(?:[^\\(\\)]|(R))+(?:\\'|\\\"|\\]|null|\\s*)\\))/", "''", $f);
                }
                $bar_finder->advance();
            }
            $bar_path->advance();
        }
        
        $bar_finder->finish();
        $bar_path->finish();

        $keys = array_unique($keys);

        $bar = $this->output->createProgressBar(count($keys));
        foreach ($keys as $key => $match) {
            if (! preg_match('/(lang(?:(\s?|\s+))\()(?:(\s?|\s+))(?:(\\$))/', $match))
            {

                // if it is not a dynamic variable
                preg_match_all('/lang(?:\s*)\((?:(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\"))(?:(?:\s*)(?:\,(?:\s*)((?:\[|array\().*(?:\]|\))|null)(?:\s*)(?:\,(?:\s*)(?:(\d+?|null))(?:\s*)(?:\,(?:\s*)(?:\\\'|\")(.*?)(?:\\\'|\")(?:\s*)?)?)?)?)\)/', $match, $d);
                
                if (!empty($d[1][0]) and (!empty($d[4][0]) and ($d[4][0] != 'null')))
                {
                    $insert = lang($d[1][0], null, null, $d[4][0], null, true);
                    //$this->info('INSERTING: "'.$d[1][0].'" on "'.$d[4][0].'" group.');
                } elseif (!empty($d[1][0])) {
                    $insert = lang($d[1][0], null, null, 'general', null, true);
                    //$this->info('INSERTING: "'.$d[1][0].'" on "general" group.');
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Completed!');
    }
}
