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

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

class CoreResolver
{
    /** @var string */
    private $ci3CoreNamespace = 'Kenjis\CI3Compatible\Core';

    /** @var string */
    private $prefix = 'CI_';

    public function resolve(string $class): string
    {
        return $this->ci3CoreNamespace . '\\' . $this->prefix . $class;
    }
}
