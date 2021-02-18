<?php

declare(strict_types=1);

use Kenjis\CI3Compatible\Core\CI_Controller;

if (loadTestBootstrap() === false) {
    throw new RuntimeException(
        'Cannot find "system/Test/bootstrap.php" of CI4 in ' . __FILE__
    );
}

new CI_Controller();

function loadTestBootstrap()
{
    $testBootsraps = [
        __DIR__ . '/../../../../../codeigniter4/framework/system/Test/bootstrap.php',
        __DIR__ . '/../../../../../codeigniter4/codeigniter4/system/Test/bootstrap.php',
    ];

    foreach ($testBootsraps as $bootstrap) {
        if (file_exists($bootstrap)) {
            require $bootstrap;

            return true;
        }
    }

    return false;
}
