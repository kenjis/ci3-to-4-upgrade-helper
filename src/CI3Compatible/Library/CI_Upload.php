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

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Images\Exceptions\ImageException;
use CodeIgniter\Images\Image;
use Config\Services;
use Kenjis\CI3Compatible\Exception\NotImplementedException;
use Kenjis\CI3Compatible\Library\Upload\ValidationRuleMaker;

use function realpath;
use function round;
use function strlen;
use function substr;

class CI_Upload
{
    /** @var ValidationRuleMaker */
    private $ruleMaker;

    /** @var array */
    private $ci3Config;

    /** @var UploadedFile|null */
    private $file;

    /**
     * Constructor
     *
     * @param   array $config
     *
     * @return  void
     */
    public function __construct(array $config = [])
    {
        $this->ci3Config = $config;
        $this->ruleMaker = new ValidationRuleMaker();

        $this->checkNotImplementedConfig();
    }

    private function checkNotImplementedConfig()
    {
        // @TODO
        $notImplemented = [
            'file_name',
            'file_ext_tolower',
            'max_filename',
            'max_filename_increment',
            'remove_spaces',
            'detect_mime',
            'mod_mime_fix',
        ];

        foreach ($notImplemented as $item) {
            if (isset($this->ci3Config[$item])) {
                throw new NotImplementedException(
                    'config "' . $item . '" is not implemented yet.'
                );
            }
        }
    }

    /**
     * Perform the file upload
     *
     * @param   string $field
     *
     * @return  bool
     */
    public function do_upload(string $field = 'userfile')
    {
        $validation = Services::validation();
        $request = Services::request();

        $rules = $this->ruleMaker->convert($field, $this->ci3Config);
        $isValid = $validation->withRequest($request)->setRules($rules)->run();

        if (! $isValid) {
            return false;
        }

        $this->file = $request->getFile($field);

        if ($this->file !== null) {
            if ($this->file->isValid() && ! $this->file->hasMoved()) {
                $overwrite = $this->ci3Config['overwrite'] ?? false;

                if ($this->ci3Config['encrypt_name']) {
                    $newName = $this->file->getRandomName();
                    $this->file->move($this->ci3Config['upload_path'], $newName, $overwrite);
                } else {
                    $this->file->move($this->ci3Config['upload_path'], null, $overwrite);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Finalized Data Array
     *
     * Returns an associative array containing all of the information
     * related to the upload, allowing the developer easy access in one array.
     *
     * @param   string $index
     *
     * @return  mixed
     */
    public function data(?string $index = null)
    {
        $full_path = realpath(
            $this->ci3Config['upload_path'] . '/' . $this->file->getName()
        );

        $data = $this->getImageData($full_path);

        if (! empty($index)) {
            return $data[$index] ?? null;
        }

        return $data;
    }

    private function getImageData(string $full_path): array
    {
        $image = new Image($full_path);

        try {
            $imageData = $image->getProperties(true);
            $imageData['is_image'] = true;

            $types = [1 => 'gif', 2 => 'jpeg', 3 => 'png'];
            $image_type = $types[$imageData['image_type']] ?? 'unknown';
        } catch (ImageException $e) {
            $imageData = [
                'is_image' => false,
                'width'    => null,
                'height'   => null,
                'size_str' => '',
            ];
            $image_type = '';
        }

        $file_ext = '.' . $this->file->getClientExtension();

        $raw_name = substr(
            $this->file->getName(),
            0,
            -strlen($file_ext)
        );

        $file_size = $this->file->getSize();
        if ($file_size > 0) {
            $file_size = round($file_size / 1024, 2);
        }

        return [
            'file_name'      => $this->file->getName(),
            'file_type'      => $this->file->getClientMimeType(),
            'file_path'      => realpath($this->ci3Config['upload_path']),
            'full_path'      => $full_path,
            'raw_name'       => $raw_name,
            'orig_name'      => $this->file->getClientName(),
            'client_name'    => $this->file->getClientName(),
            'file_ext'       => $file_ext,
            'file_size'      => $file_size,
            'is_image'       => $imageData['is_image'],
            'image_width'    => $imageData['width'],
            'image_height'   => $imageData['height'],
            'image_type'     => $image_type,
            'image_size_str' => $imageData['size_str'],
        ];
    }
}
