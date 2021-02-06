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

namespace Kenjis\CI3Compatible\Traits\View;

use function get_instance;

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
