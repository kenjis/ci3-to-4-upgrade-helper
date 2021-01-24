<?php

declare(strict_types=1);

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
