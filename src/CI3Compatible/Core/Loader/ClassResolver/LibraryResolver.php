<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader\ClassResolver;

use function in_array;
use function ucfirst;

class LibraryResolver
{
    /** @var string */
    private $ci3LibraryNamespace = 'Kenjis\CI3Compatible\Library';

    /** @var string[] */
    private $ci3Libraries = [
        'cache',
        'email',
        'encryption',
        'form_validation',
        'image_lib',
        'pagination',
        'session',
        'upload',
        'user_agent',
    ];

    /** @var string */
    private $prefix = 'CI_';

    /** @var string */
    private $userLibraryNamespace = 'App\Libraries';

    public function resolve(string $library): string
    {
        $classname = $this->resolveCI3Library($library);

        if ($classname === null) {
            $classname = $this->resolveUserLibrary($library);
        }

        return $classname;
    }

    private function resolveCI3Library(string $library): ?string
    {
        $classname = $this->prefix . ucfirst($library);
        if (in_array($library, $this->ci3Libraries, true)) {
            return $this->ci3LibraryNamespace . '\\' . $classname;
        }

        return null;
    }

    private function resolveUserLibrary(string $library): string
    {
        return $this->userLibraryNamespace . '\\' . ucfirst($library);
    }
}
