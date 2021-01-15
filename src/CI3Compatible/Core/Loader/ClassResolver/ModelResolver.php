<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

use function ucfirst;

class ModelResolver
{
    /** @var string */
    private $namespace = 'App\\Models';

    public function resolve(string $model): string
    {
        return $this->namespace . '\\' . ucfirst($model);
    }
}
