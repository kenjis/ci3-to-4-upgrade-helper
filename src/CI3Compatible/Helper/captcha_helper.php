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

use Kenjis\CI3Like\Captcha\Captcha;

if (! function_exists('create_captcha')) {
    /**
     * Create CAPTCHA
     *
     * @param   array|string $data      Data for the CAPTCHA
     * @param   string       $img_path  Path to create the image in (deprecated)
     * @param   string       $img_url   URL to the CAPTCHA image folder (deprecated)
     * @param   string       $font_path Server path to font (deprecated)
     *
     * @return  array{word: string, time: float, image: string, filename: string}
     */
    function create_captcha(
        $data = '',
        string $img_path = '',
        string $img_url = '',
        string $font_path = ''
    ): array {
        return Captcha::createCaptcha($data, $img_path, $img_url, $font_path);
    }
}
