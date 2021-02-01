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

namespace Kenjis\CI3Compatible\Internal;

use function array_pop;
use function explode;

/**
 * @internal
 */
class DebugLog
{
    public static function log(string $classAndMethod, string $message)
    {
        $path = explode('\\', $classAndMethod);
        $method = array_pop($path);

        log_message('debug', '[' . $method . '] ' . $message);
    }
}
