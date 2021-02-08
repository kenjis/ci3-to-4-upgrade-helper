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

namespace Kenjis\CI3Compatible\Library\Upload;

use function count;
use function end;
use function explode;
use function strtolower;

class FileExtention
{
    public function toLower(string $filename)
    {
        $parts = explode('.', $filename, 2);

        if (count($parts) === 1) {
            return $filename;
        }

        $ext = strtolower(end($parts));

        return $parts[0] . '.' . $ext;
    }
}
