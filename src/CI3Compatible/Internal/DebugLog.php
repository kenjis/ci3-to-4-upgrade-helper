<?php

declare(strict_types=1);

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
