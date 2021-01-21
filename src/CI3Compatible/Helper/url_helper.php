<?php

declare(strict_types=1);

use CodeIgniter\HTTP\URI;
use Config\Services;

if (! function_exists('base_url_')) {
    /**
     * Return the base URL without rtrim('/')
     *
     * @param  mixed  $uri      URI string or array of URI segments
     * @param  string $protocol
     *
     * @return string
     */
    function base_url_($uri = '', ?string $protocol = null): string
    {
        // convert segment array to string
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        $uri = trim($uri, '/');

        // We should be using the configured baseURL that the user set;
        // otherwise get rid of the path, because we have
        // no way of knowing the intent...
        $config = Services::request()->config;

        // If baseUrl does not have a trailing slash it won't resolve
        // correctly for users hosting in a subfolder.
        $baseUrl = ! empty($config->baseURL) && $config->baseURL !== '/'
            ? rtrim($config->baseURL, '/ ') . '/'
            : $config->baseURL;

        $url = new URI($baseUrl);
        unset($config);

        // Merge in the path set by the user, if any
        if (! empty($uri)) {
            $url = $url->resolveRelativeURI($uri);
        }

        // If the scheme wasn't provided, check to
        // see if it was a secure request
        if (empty($protocol) && Services::request()->isSecure()) {
            $protocol = 'https';
        }

        if (! empty($protocol)) {
            $url->setScheme($protocol);
        }

        return rtrim((string) $url, ' ');
    }
}
