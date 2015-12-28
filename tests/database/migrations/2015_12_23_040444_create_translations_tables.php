<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->boolean('confirmed')->default(0);
            $table->string('confirmation_code')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        /**
         * First lets add the language column to the user table if it does not exist
         */
        if (!Schema::hasColumn('users', 'language')) {
            Schema::table('users', function ($table) {
                $table->string('language', 5)->default('en');
            });
        }

        /**
         * Create the languages table
         */
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->increments('id');
                $table->text('name');
                $table->string('iso')->unique();
                $table->boolean('status')->default(1);
                $table->text('original_name')->nullable();
                $table->timestamps();
            });
        }
        if (Schema::hasTable('languages')) {
            /**
             * Populate the table with the original languages
             */
            DB::table('languages')->insert(
                array(
                    array('name' => 'Afrikaans', 'iso' => 'af', 'status' => '1', 'original_name' => 'Afrikaans'),
                    array('name' => 'Arabic', 'iso' => 'ar', 'status' => '1', 'original_name' => 'العربية'),
                    array('name' => 'Aymara', 'iso' => 'ay', 'status' => '1', 'original_name' => 'Aymar aru'),
                    array('name' => 'Azeri', 'iso' => 'az', 'status' => '1', 'original_name' => 'آذربایجانلیلار / آذری لر'),
                    array('name' => 'Belarusian', 'iso' => 'be', 'status' => '1', 'original_name' => 'Беларуская мова'),
                    array('name' => 'Bulgarian', 'iso' => 'bg', 'status' => '1', 'original_name' => 'български'),
                    array('name' => 'Bengali', 'iso' => 'bn', 'status' => '1', 'original_name' => 'বাংলা'),
                    array('name' => 'Bosnian', 'iso' => 'bs', 'status' => '1', 'original_name' => 'Bosanski'),
                    array('name' => 'Catalan', 'iso' => 'ca', 'status' => '1', 'original_name' => 'Català'),
                    array('name' => 'Cherokee', 'iso' => 'ck', 'status' => '1', 'original_name' => 'ᎠᏂᏴᏫᏯᎢ'),
                    array('name' => 'Czech', 'iso' => 'cs', 'status' => '1', 'original_name' => 'Čeština'),
                    array('name' => 'Welsh', 'iso' => 'cy', 'status' => '1', 'original_name' => 'Cymraeg'),
                    array('name' => 'Danish', 'iso' => 'da', 'status' => '1', 'original_name' => 'Dansk'),
                    array('name' => 'German', 'iso' => 'de', 'status' => '1', 'original_name' => 'Deutsch'),
                    array('name' => 'Dhivehi', 'iso' => 'dv', 'status' => '1', 'original_name' => 'Dhivehi'),
                    array('name' => 'Greek', 'iso' => 'el', 'status' => '1', 'original_name' => 'Ελληνικά'),
                    array('name' => 'English', 'iso' => 'en', 'status' => '1', 'original_name' => 'English'),
                    array('name' => 'Esperanto', 'iso' => 'eo', 'status' => '1', 'original_name' => 'Esperanto'),
                    array('name' => 'Spanish', 'iso' => 'es', 'status' => '1', 'original_name' => 'Español'),
                    array('name' => 'Estonian', 'iso' => 'et', 'status' => '1', 'original_name' => 'Eesti'),
                    array('name' => 'Basque', 'iso' => 'eu', 'status' => '1', 'original_name' => 'Euskara'),
                    array('name' => 'Persian', 'iso' => 'fa', 'status' => '1', 'original_name' => 'فارسی'),
                    array('name' => 'Finnish', 'iso' => 'fi', 'status' => '1', 'original_name' => 'Suomi'),
                    array('name' => 'Faroese', 'iso' => 'fo', 'status' => '1', 'original_name' => 'føroyskt'),
                    array('name' => 'French', 'iso' => 'fr', 'status' => '1', 'original_name' => 'Français'),
                    array('name' => 'Irish', 'iso' => 'ga', 'status' => '1', 'original_name' => 'Gaeilge'),
                    array('name' => 'Galician', 'iso' => 'gl', 'status' => '1', 'original_name' => 'Galego'),
                    array('name' => "Guaran'ed", 'iso' => 'gn', 'status' => '1', 'original_name' => "Guaran'ed"),
                    array('name' => 'Gujarati', 'iso' => 'gu', 'status' => '1', 'original_name' => 'ગુજરાતી'),
                    array('name' => 'Hebrew', 'iso' => 'he', 'status' => '1', 'original_name' => 'עִבְרִית'),
                    array('name' => 'Hindi', 'iso' => 'hi', 'status' => '1', 'original_name' => 'हिन्दी'),
                    array('name' => 'Croatian', 'iso' => 'hr', 'status' => '1', 'original_name' => 'Hrvatski'),
                    array('name' => 'Hungarian', 'iso' => 'hu', 'status' => '1', 'original_name' => 'Magyar'),
                    array('name' => 'Armenian', 'iso' => 'hy', 'status' => '1', 'original_name' => 'Հայերէն'),
                    array('name' => 'Indonesian', 'iso' => 'id', 'status' => '1', 'original_name' => 'Bahasa Indonesia'),
                    array('name' => 'Icelandic', 'iso' => 'is', 'status' => '1', 'original_name' => 'Íslenska'),
                    array('name' => 'Italian', 'iso' => 'it', 'status' => '1', 'original_name' => 'Italiano'),
                    array('name' => 'Japanese', 'iso' => 'ja', 'status' => '1', 'original_name' => '日本語'),
                    array('name' => 'Javanese', 'iso' => 'jv', 'status' => '1', 'original_name' => 'Basa Jawi'),
                    array('name' => 'Georgian', 'iso' => 'ka', 'status' => '1', 'original_name' => 'ქართული'),
                    array('name' => 'Kazakh', 'iso' => 'kk', 'status' => '1', 'original_name' => 'قازاق تىلى'),
                    array('name' => 'Khmer', 'iso' => 'km', 'status' => '1', 'original_name' => 'ភាសាខ្មែរ'),
                    array('name' => 'Kannada', 'iso' => 'kn', 'status' => '1', 'original_name' => 'ಕನ್ನಡ'),
                    array('name' => 'Korean', 'iso' => 'ko', 'status' => '1', 'original_name' => '한국어'),
                    array('name' => 'Kurdish', 'iso' => 'ku', 'status' => '1', 'original_name' => 'كوردی '),
                    array('name' => 'Latin', 'iso' => 'la', 'status' => '1', 'original_name' => 'lingua Latina'),
                    array('name' => 'Limburgish', 'iso' => 'li', 'status' => '1', 'original_name' => 'Limburgs'),
                    array('name' => 'Lithuanian', 'iso' => 'lt', 'status' => '1', 'original_name' => 'Lietuvių'),
                    array('name' => 'Latvian', 'iso' => 'lv', 'status' => '1', 'original_name' => 'latviešu'),
                    array('name' => 'Malagasy', 'iso' => 'mg', 'status' => '1', 'original_name' => 'Malagasy'),
                    array('name' => 'Macedonian', 'iso' => 'mk', 'status' => '1', 'original_name' => 'македонски'),
                    array('name' => 'Malayalam', 'iso' => 'ml', 'status' => '1', 'original_name' => 'മലയാളം'),
                    array('name' => 'Mongolian', 'iso' => 'mn', 'status' => '1', 'original_name' => 'Монгол'),
                    array('name' => 'Marathi', 'iso' => 'mr', 'status' => '1', 'original_name' => 'मराठी'),
                    array('name' => 'Malay', 'iso' => 'ms', 'status' => '1', 'original_name' => 'بهاس ملاي'),
                    array('name' => 'Maltese', 'iso' => 'mt', 'status' => '1', 'original_name' => 'Malti'),
                    array('name' => 'Norwegian (bokmal)', 'iso' => 'nb', 'status' => '1', 'original_name' => 'Norsk (Bokmål)'),
                    array('name' => 'Nepali', 'iso' => 'ne', 'status' => '1', 'original_name' => 'नेपाली'),
                    array('name' => 'Dutch', 'iso' => 'nl', 'status' => '1', 'original_name' => 'Nederlands'),
                    array('name' => 'Norwegian (nynorsk)', 'iso' => 'nn', 'status' => '1', 'original_name' => 'Norsk (Nynorsk)'),
                    array('name' => 'Punjabi', 'iso' => 'pa', 'status' => '1', 'original_name' => 'ਪੰਜਾਬੀ, पंजाबी, پنجابی, Pañjābī'),
                    array('name' => 'Polish', 'iso' => 'pl', 'status' => '1', 'original_name' => 'Polski'),
                    array('name' => 'Pashto', 'iso' => 'ps', 'status' => '1', 'original_name' => 'پښتو'),
                    array('name' => 'Portuguese', 'iso' => 'pt', 'status' => '1', 'original_name' => 'Português'),
                    array('name' => 'Quechua', 'iso' => 'qu', 'status' => '1', 'original_name' => 'Qhichwa Simi'),
                    array('name' => 'Romansh', 'iso' => 'rm', 'status' => '1', 'original_name' => 'Rumantsch'),
                    array('name' => 'Romanian', 'iso' => 'ro', 'status' => '1', 'original_name' => 'Română'),
                    array('name' => 'Russian', 'iso' => 'ru', 'status' => '1', 'original_name' => 'Русский'),
                    array('name' => 'Sanskrit', 'iso' => 'sa', 'status' => '1', 'original_name' => 'संस्कृतम्'),
                    array('name' => 'Northern Sami', 'iso' => 'se', 'status' => '1', 'original_name' => 'Davvisámegiella / Sámegiella'),
                    array('name' => 'Slovak', 'iso' => 'sk', 'status' => '1', 'original_name' => 'Slovenčina'),
                    array('name' => 'Slovenian', 'iso' => 'sl', 'status' => '1', 'original_name' => 'Slovenščina'),
                    array('name' => 'Somali', 'iso' => 'so', 'status' => '1', 'original_name' => 'الصومالية'),
                    array('name' => 'Albanian', 'iso' => 'sq', 'status' => '1', 'original_name' => 'Shqip'),
                    array('name' => 'Serbian', 'iso' => 'sr', 'status' => '1', 'original_name' => 'српски'),
                    array('name' => 'Swedish', 'iso' => 'sv', 'status' => '1', 'original_name' => 'Svenska'),
                    array('name' => 'Swahili', 'iso' => 'sw', 'status' => '1', 'original_name' => 'Kiswahili'),
                    array('name' => 'Syriac', 'iso' => 'sy', 'status' => '1', 'original_name' => 'leššānā Suryāyā'),
                    array('name' => 'Tamil', 'iso' => 'ta', 'status' => '1', 'original_name' => 'தமிழ்'),
                    array('name' => 'Telugu', 'iso' => 'te', 'status' => '1', 'original_name' => 'తెలుగు'),
                    array('name' => 'Tajik', 'iso' => 'tg', 'status' => '1', 'original_name' => 'тоҷикӣ/ تاجیکی‎ / tojikī'),
                    array('name' => 'Thai', 'iso' => 'th', 'status' => '1', 'original_name' => 'ภาษาไทย'),
                    array('name' => 'Filipino', 'iso' => 'tl', 'status' => '1', 'original_name' => 'Pilipino'),
                    array('name' => 'Turkish', 'iso' => 'tr', 'status' => '1', 'original_name' => 'Türkçe'),
                    array('name' => 'Tatar', 'iso' => 'tt', 'status' => '1', 'original_name' => 'татарча / Tatarça / تاتارچا'),
                    array('name' => 'Ukrainian', 'iso' => 'uk', 'status' => '1', 'original_name' => 'українська'),
                    array('name' => 'Urdu', 'iso' => 'ur', 'status' => '1', 'original_name' => 'اردو'),
                    array('name' => 'Uzbek', 'iso' => 'uz', 'status' => '1', 'original_name' => 'O‘zbek / Ўзбек / أۇزبېك'),
                    array('name' => 'Vietnamese', 'iso' => 'vi', 'status' => '1', 'original_name' => 'Tiếng Việt'),
                    array('name' => 'Xhosa', 'iso' => 'xh', 'status' => '1', 'original_name' => 'isiXhosa'),
                    array('name' => 'Yiddish', 'iso' => 'yi', 'status' => '1', 'original_name' => 'ייִדיש / yidish'),
                    array('name' => 'Simplified Chinese (China)', 'iso' => 'zh', 'status' => '1', 'original_name' => '中文(简体)'),
                    array('name' => 'Zulu', 'iso' => 'zu', 'status' => '1', 'original_name' => 'isiZulu')
                )
            );
            DB::statement('UPDATE '.'languages'.' SET created_at = NOW(), updated_at = NOW() WHERE 1');
        }
        /**
         * Create the variables table
         */
        if (!Schema::hasTable('translations_variables')) {
            Schema::create('translations_variables', function (Blueprint $table) {
                $table->increments('id');
                $table->text('text');
                $table->text('group');
                $table->string('md5sha1', 255);
                $table->boolean('dynamic')->default(0);
                $table->timestamps();
            });
        }
        /**
         * Create the translated table
         */
        if (!Schema::hasTable('translations_translated')) {
            Schema::create('translations_translated', function (Blueprint $table) {
                $table->integer('variable_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->text('translation');
                $table->timestamps();
                $table->foreign('variable_id')->references('id')->on('translations_variables')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade')->onDelete('cascade');
                $table->primary(['variable_id', 'language_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations_translated');
        Schema::drop('translations_variables');
        Schema::drop('languages');
        if (Schema::hasColumn('users', 'language')) {
            Schema::table('users', function ($table) {
                $table->drop('language');
            });
        }
    }
}
