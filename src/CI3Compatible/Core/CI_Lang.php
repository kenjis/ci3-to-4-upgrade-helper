<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

namespace Kenjis\CI3Compatible\Core;

use LogicException;

use function array_merge;
use function file_exists;
use function is_array;
use function preg_match;
use function preg_replace;
use function realpath;
use function str_replace;

class CI_Lang
{
    /** @var array */
    private $langMap = [
        'ar' => 'arabic',
        'hy' => 'armenian',
        'az' => 'azerbaijani',
        'eu' => 'basque',
        'bn' => 'bengali',
        'bs' => 'bosnian',
        'bg' => 'bulgarian',
        'ca' => 'catalan',
        'hr' => 'croatian',
        'cs' => 'czech',
        'da' => 'danish',
        'nl' => 'dutch',
        'en' => 'english',
        'tl' => 'filipino',
        'fi' => 'finnish',
        'fr' => 'french',
        'de' => 'german',
        'el' => 'greek',
        'gu' => 'gujarati',
        'hi' => 'hindi',
        'hu' => 'hungarian',
        'id' => 'indonesian',
        'it' => 'italian',
        'ja' => 'japanese',
        'km' => 'khmer',
        'ko' => 'korean',
        'lv' => 'latvian',
        'lt' => 'lithuanian',
        'mr' => 'marathi',
        'no' => 'norwegian',
        'fa' => 'persian',
        'pl' => 'polish',
        'pt' => 'portuguese',
        'pt-BR' => 'portuguese-brazilian',
        'ro' => 'romanian',
        'ru' => 'russian',
        'sr' => 'serbian',
        'zh-CN' => 'simplified-chinese',
        'sk' => 'slovak',
        'sl' => 'slovenian',
        'es' => 'spanish',
        'sv' => 'swedish',
        'ta' => 'tamil',
        'th' => 'thai',
        'zh-TW' => 'traditional-chinese',
        'tr' => 'turkish',
        'uk' => 'ukrainian',
        'ur' => 'urdu',
        'vi' => 'vietnamese',
    ];

    /**
     * List of language folder paths
     *
     * @var string[]
     */
    private $langPaths;

    /**
     * List of translations
     *
     * @var array
     */
    private $language = [];

    /**
     * List of loaded language files
     *
     * @var array
     */
    private $isLoaded = [];

    public function __construct(array $langPaths = [])
    {
        $this->langPaths = $langPaths;
    }

    /**
     * The core classes are loaded via Composer autoloader. So we can't use
     * `APPPATH` when this class is instantiated.
     */
    private function setDefaultLangPaths()
    {
        if ($this->langPaths === []) {
            $this->langPaths = [
                realpath(APPPATH . 'Language'),
            ];
        }
    }

    /**
     * Load a language file
     *
     * @param   mixed  $langfile   Language file name
     * @param   string $idiom      Language name (english, etc.)
     * @param   bool   $return     Whether to return the loaded array of translations
     * @param   bool   $add_suffix Whether to add suffix to $langfile
     * @param   string $alt_path   Alternative path to look for the language file
     *
     * @return  void|bool|string[]   Array containing translations, if $return is set to TRUE
     */
    public function load(
        $langfile,
        string $idiom = '',
        bool $return = false,
        bool $add_suffix = true,
        string $alt_path = ''
    ) {
        $this->setDefaultLangPaths();

        if (is_array($langfile)) {
            foreach ($langfile as $value) {
                $this->load($value, $idiom, $return, $add_suffix, $alt_path);
            }

            return;
        }

        $langfile = $this->getLangFilename($langfile, $add_suffix);
        $idiom = $this->getIdiom($idiom);

        if (
            $return === false && isset($this->isLoaded[$langfile])
            && $this->isLoaded[$langfile] === $idiom
        ) {
            return;
        }

        [$lang, $found] = $this->loadLangFile($idiom, $langfile);

        if ($found !== true) {
            throw new LogicException(
                'Unable to load the requested language file: ' . $idiom . '/' . $langfile
            );
        }

        if ($lang === []) {
            log_message(
                'error',
                'Language file contains no data: ' . $idiom . '/' . $langfile
            );

            if ($return === true) {
                return [];
            }

            return;
        }

        if ($return === true) {
            return $lang;
        }

        $this->isLoaded[$langfile] = $idiom;
        $this->language = array_merge($this->language, $lang);

        return true;
    }

    private function getLangFilename(string $langfile, bool $add_suffix): string
    {
        $langfile = str_replace('.php', '', $langfile);

        if ($add_suffix === true) {
            $langfile = preg_replace('/_lang$/', '', $langfile) . '_lang';
        }

        $langfile .= '.php';

        return $langfile;
    }

    private function getIdiom(string $idiom): string
    {
        if (empty($idiom) || ! preg_match('/^[a-z_-]+$/i', $idiom)) {
            $config = config('App');
            $defaultLocale = $config->defaultLocale;

            $idiom = $this->langMap[$defaultLocale] ?? 'english';
        }

        return $idiom;
    }

    private function loadLangFile(string $idiom, string $langfile): array
    {
        $lang = [];
        $found = false;

        foreach ($this->langPaths as $path) {
            $path .= '/' . $idiom . '/' . $langfile;

            if (file_exists($path)) {
                include $path;
                $found = true;
                break;
            }
        }

        return [$lang, $found];
    }

    /**
     * Language line
     *
     * Fetches a single line of text from the language array
     *
     * @param   string $line       Language line key
     * @param   bool   $log_errors Whether to log an error message if the line is not found
     *
     * @return  string|false  Translation
     */
    public function line(string $line, bool $log_errors = true)
    {
        $value = $this->language[$line] ?? false;

        if ($value === false && $log_errors === true) {
            log_message('error', 'Could not find the language line "' . $line . '"');
        }

        return $value;
    }
}
