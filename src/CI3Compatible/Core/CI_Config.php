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

use CodeIgniter\Config\BaseConfig;
use Kenjis\CI3Compatible\Exception\NotImplementedException;
use Kenjis\CI3Compatible\Exception\RuntimeException;

use function array_key_exists;
use function array_unshift;
use function property_exists;
use function site_url;

class CI_Config
{
    /** @var BaseConfig[] */
    private $configs = [];

    /** @var BaseConfig[] */
    private $configsWithSection;

    /**
     * Load Config File
     *
     * @param   string $file            Configuration file name
     * @param   bool   $use_sections    Whether configuration values should be loaded into their own section
     * @param   bool   $fail_gracefully Whether to just return FALSE or display an error message
     *
     * @return  bool    TRUE if the file was loaded correctly or FALSE on failure
     */
    public function load(string $file = '', bool $use_sections = false, bool $fail_gracefully = false)
    {
        if ($fail_gracefully !== false) {
            throw new NotImplementedException(
                '$fail_gracefully is not implemented yet.'
            );
        }

        $config = config($file);

        if ($config === null) {
            throw new RuntimeException(
                'Cannot find Config class "' . $file . '".'
                . ' Fix your config name.'
            );
        }

        if ($use_sections) {
            $this->configsWithSection[$file] = $config;

            return true;
        }

        array_unshift($this->configs, $config);

        return true;
    }

    /**
     * Fetch a config file item
     *
     * @param   string $item  Config item name
     * @param   string $index Index name
     *
     * @return  string|null The configuration item or NULL if the item doesn't exist
     */
    public function item(string $item, string $index = '')
    {
        if ($index !== '') {
            if (array_key_exists($index, $this->configsWithSection)) {
                if (property_exists($this->configsWithSection[$index], $item)) {
                    return $this->configsWithSection[$index]->$item;
                }
            }

            throw new RuntimeException(
                'Cannot find config "' . $item . '" in "' . $index . '".'
                . ' Check your Config class name or property name.'
            );
        }

        foreach ($this->configs as $config) {
            if (property_exists($config, $item)) {
                return $config->$item;
            }
        }

        throw new RuntimeException(
            'Cannot find config "' . $item . '"'
            . ' Check your Config class name.'
        );
    }

    /**
     * Site URL
     *
     * Returns base_url . index_page [. uri_string]
     *
     * @param   string|string[] $uri      URI string or an array of segments
     * @param   string          $protocol
     *
     * @return  string
     *
     * @uses    CI_Config::_uri_string()
     */
    public function site_url($uri = '', ?string $protocol = null): string
    {
        helper('url');

        return site_url($uri, $protocol);
    }
}
