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

use Kenjis\CI3Compatible\Core\CoreLoader;

require __DIR__ . '/../CI3Compatible/Core/get_instance.php';
require __DIR__ . '/../CI3Compatible/Core/Common.php';

$coreLoader = new CoreLoader();
