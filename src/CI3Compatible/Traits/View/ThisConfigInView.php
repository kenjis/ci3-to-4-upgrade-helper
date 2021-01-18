<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Traits\View;

/**
 * Use in app/Config/View.php
 *
 * If you use `$this->config` in view files, you need this.
 */
trait ThisConfigInView
{
    public function __call($method, $params)
    {
        $controller = get_instance();

        return $controller->config->$method(...$params);
    }
}
