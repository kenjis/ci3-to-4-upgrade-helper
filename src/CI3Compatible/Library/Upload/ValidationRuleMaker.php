<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library\Upload;

use function explode;
use function implode;

class ValidationRuleMaker
{
    /** @var string */
    private $fieldName;

    /** @var array */
    private $ci3Config;

    /** @var array */
    private $rules = [];

    public function convert(string $fieldName, array $ci3Config)
    {
        $this->fieldName = $fieldName;
        $this->ci3Config = $ci3Config;
        $this->rules = [];

        $this->rules[] = 'uploaded[' . $fieldName . ']';

        $this->setExtIn();
        $this->setMaxSize();
        $this->setMaxDims();

// @TODO
//        $this->ci3Config['min_width']
//        $this->ci3Config['min_height']

        return [
            $fieldName => implode('|', $this->rules),
        ];
    }

    private function setExtIn()
    {
        if (isset($this->ci3Config['allowed_types'])) {
            $extsArray = explode('|', $this->ci3Config['allowed_types']);
            $exts = implode(',', $extsArray);
            $this->rules[] = 'ext_in[' . $this->fieldName . ',' . $exts . ']';
        }
    }

    private function setMaxSize()
    {
        if (isset($this->ci3Config['max_size'])) {
            $this->rules[] = 'max_size[' . $this->fieldName . ','
                . $this->ci3Config['max_size'] . ']';
        }
    }

    private function setMaxDims()
    {
        $maxWidth = $this->ci3Config['max_width'] ?? 0;
        $maxHeight = $this->ci3Config['max_height'] ?? 0;

        if ($maxWidth === 0 && $maxHeight === 0) {
            return;
        }

        $this->rules[] = 'max_dims[' . $this->fieldName . ','
            . $this->ci3Config['max_width'] . ','
            . $this->ci3Config['max_height'] . ']';
    }
}
