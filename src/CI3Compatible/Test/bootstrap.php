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

use Kenjis\CI3Compatible\Core\CI_Controller;

if (loadTestBootstrap() === false) {
    throw new RuntimeException(
        'Cannot find "system/Test/bootstrap.php" of CI4 in ' . __FILE__
    );
}

new CI_Controller();

function loadTestBootstrap()
{
    $testBootstraps = [
        __DIR__ . '/../../../../../codeigniter4/framework/system/Test/bootstrap.php',
        __DIR__ . '/../../../../../codeigniter4/codeigniter4/system/Test/bootstrap.php',
    ];

    foreach ($testBootstraps as $bootstrap) {
        if (file_exists($bootstrap)) {
            require $bootstrap;

            return true;
        }
    }

    return false;
}
