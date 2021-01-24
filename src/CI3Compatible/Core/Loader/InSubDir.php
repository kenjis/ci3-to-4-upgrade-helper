<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use function strrpos;

trait InSubDir
{
    private function inSubDir(string $component): bool
    {
        return strrpos($component, '/') !== false;
    }
}
