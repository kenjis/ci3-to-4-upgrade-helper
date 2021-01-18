<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use function site_url;

class CI_Config
{
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
