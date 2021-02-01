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

use Gregwar\Captcha\CaptchaBuilder;

if (! function_exists('create_captcha')) {
    /**
     * Create CAPTCHA
     *
     * @param   array|string $data      Data for the CAPTCHA
     * @param   string       $img_path  Path to create the image in (deprecated)
     * @param   string       $img_url   URL to the CAPTCHA image folder (deprecated)
     * @param   string       $font_path Server path to font (deprecated)
     *
     * @return  array
     */
    function create_captcha(
        $data = '',
        string $img_path = '',
        string $img_url = '',
        string $font_path = ''
    ): array {
        $now = microtime(true);

        if (isset($data['img_path'])) {
            $img_path = $data['img_path'];
        }

        if (isset($data['img_url'])) {
            $img_url = $data['img_url'];
        }

        $word = $data['word'] ?? null;
        $img_filename = $now . '.png';
        $img_id = $data['img_id'] ?? '';
        $img_src = rtrim($img_url, '/') . '/' . $img_filename;
        $img_width = $data['img_width'] ?? 150;
        $img_height = $data['img_height'] ?? 30;
        $img_alt = $data['img_alt'] ?? 'captcha';
        $img = '<img ' . ($img_id === '' ? '' : 'id="' . $img_id . '"')
            . ' src="' . $img_src . '" style="width: ' . $img_width
            . 'px; height: ' . $img_height . 'px; border: 0;" alt="'
            . $img_alt . '" />';

        $builder = new CaptchaBuilder($word);
        $builder->build();
        $builder->save($img_path . $img_filename);

        return [
            'word' => $word ?? $builder->getPhrase(),
            'time' => $now,
            'image' => $img,
            'filename' => $img_filename,
        ];
    }
}
