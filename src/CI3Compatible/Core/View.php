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

use AllowDynamicProperties;
use CodeIgniter\View\View as CI4View;

/**
 * View Proxy
 */
#[AllowDynamicProperties]
class View
{
    private CI4View $__view__;

    public function __construct(CI4View $view)
    {
        $this->__view__ = $view;
    }

    public function __call($method, $params)
    {
        return $this->__view__->$method(...$params);
    }
}
