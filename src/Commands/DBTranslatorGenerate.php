<?php

namespace bernardomacedo\DBTranslator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use bernardomacedo\DBTranslator\DBTranslator;
use bernardomacedo\DBTranslator\Models\Intl;
use bernardomacedo\DBTranslator\Models\Languages;

class DBTranslatorGenerate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    //protected $name = 'dbtranslator:generate';
    protected $signature = 'dbtranslator:generate {language?} {--S|status=active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates translations for given language.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Lets get all available languages
         */
        
        if (!$this->argument('language')) {
            /**
             * This will generate to all languages
             */
            switch($this->option('status'))
            {
                case 'active':
                    $lang = Languages::whereStatus(1)->get()->toArray();
                    break;
                case 'inactive':
                    $lang = Languages::whereStatus(0)->get()->toArray();
                    break;
                case 'all':
                    $lang = Languages::all()->toArray();
                    break;
            }
            $bar = $this->output->createProgressBar(count($lang));
            foreach ($lang as $key => $language) {
                DBTranslator::generate($language['iso']);
                $bar->advance();
            }
            $bar->finish();
        } elseif ($this->argument('language')) {
            DBTranslator::generate($this->argument('language'));
        }
        $this->info('Completed!');
    }
}
