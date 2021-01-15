<?php

declare(strict_types=1);

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
