<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Services;
use Kenjis\CI3Compatible\Library\Upload\ValidationRuleMaker;

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
// @TODO
//                $this->ci3Config['file_name']
//                $this->ci3Config['file_ext_tolower']
//                $this->ci3Config['overwrite']
//                $this->ci3Config['max_filename']
//                $this->ci3Config['max_filename_increment']
//                $this->ci3Config['remove_spaces']
//                $this->ci3Config['detect_mime']
//                $this->ci3Config['mod_mime_fix']

                if ($this->ci3Config['encrypt_name']) {
                    $newName = $this->file->getRandomName();
                    $this->file->move($this->ci3Config['upload_path'], $newName);
                } else {
                    $this->file->move($this->ci3Config['upload_path']);
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
        $full_path = $this->ci3Config['upload_path'] . '/' . $this->file->getName();
        $raw_name = substr(
            $this->file->getName(),
            0,
            -strlen($this->file->getClientExtension())
        );

        $data = [
            'file_name'      => $this->file->getName(),
            'file_type'      => $this->file->getClientMimeType(),
            'file_path'      => $this->ci3Config['upload_path'],
            'full_path'      => $full_path,
            'raw_name'       => $raw_name,
            'orig_name'      => $this->file->getClientName(),
            'client_name'    => $this->file->getClientName(),
            'file_ext'       => $this->file->getClientExtension(),
            'file_size'      => $this->file->getSize(),
// @TODO
//            'is_image'       => $this->is_image(),
//            'image_width'    => $this->image_width,
//            'image_height'   => $this->image_height,
//            'image_type'     => $this->image_type,
//            'image_size_str' => $this->image_size_str,
        ];

        if (! empty($index)) {
            return $data[$index] ?? null;
        }

        return $data;
    }
}
