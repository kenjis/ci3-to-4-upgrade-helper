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

namespace Kenjis\CI3Compatible\Exception;

/**
 * Exception for Not Supported
 *
 * This function is not supported. Please migrate it manually.
 */
class NotSupportedException extends LogicException
{
}
