<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\TestSupport\TestCase;

class HelperLoaderTest extends TestCase
{
    public function test_load_one_helper(): void
    {
        $loader = $this->getDouble(
            HelperLoader::class,
            ['loadCI4Helper' => true]
        );
        $this->verifyInvokedOnce($loader, 'loadCI4Helper', ['form']);

        $loader->load('form');
    }

    public function test_load_two_helpers(): void
    {
        $loader = $this->getDouble(
            HelperLoader::class,
            ['loadCI4Helper' => true]
        );
        $this->verifyInvokedMultipleTimes(
            $loader,
            'loadCI4Helper',
            2,
            [
                ['form'],
                ['array'],
            ]
        );

        $loader->load(['form', 'array']);
    }
}
