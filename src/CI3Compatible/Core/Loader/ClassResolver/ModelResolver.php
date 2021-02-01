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

use Kenjis\CI3Compatible\Core\Loader\InSubDir;

use function explode;
use function implode;
use function ucfirst;

class ModelResolver
{
    use InSubDir;

    /** @var string */
    private $namespace = 'App\\Models';

    public function resolve(string $model): string
    {
        if ($this->inSubDir($model)) {
            $parts = explode('/', $model);

            foreach ($parts as $key => $part) {
                $parts[$key] = ucfirst($part);
            }

            return $this->namespace . '\\' . implode('\\', $parts);
        }

        return $this->namespace . '\\' . ucfirst($model);
    }
}
