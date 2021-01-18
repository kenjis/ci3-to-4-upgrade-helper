<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

use Kenjis\CI3Compatible\Exception\NotImplementedException;

use function in_array;
use function ucfirst;

class LibraryResolver
{
    /** @var string */
    private $ci3LibraryNamespace = 'Kenjis\CI3Compatible\Library';

    /** @var string[] */
    private $ci3Libraries = ['form_validation'];

    /** @var string */
    private $prefix = 'CI_';

    public function resolve(string $library): string
    {
        $classname = $this->prefix . ucfirst($library);
        if (in_array($library, $this->ci3Libraries, true)) {
            return $this->ci3LibraryNamespace . '\\' . $classname;
        }

        throw new NotImplementedException($classname . ' is not implemented.');
    }
}
