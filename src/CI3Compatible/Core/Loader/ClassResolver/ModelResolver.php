<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

use function explode;
use function implode;
use function strrpos;
use function ucfirst;

class ModelResolver
{
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

    private function inSubDir(string $model): bool
    {
        return strrpos($model, '/') !== false;
    }
}
