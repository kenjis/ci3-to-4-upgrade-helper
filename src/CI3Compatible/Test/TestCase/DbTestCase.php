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

namespace Kenjis\CI3Compatible\Test\TestCase;

use Kenjis\CI3Compatible\Test\Traits\ResetInstance;
use Kenjis\PhpUnitHelper\TestDouble;
use Tests\Support\DatabaseTestCase;

class DbTestCase extends DatabaseTestCase
{
    use ResetInstance;
    use TestDouble;
}
