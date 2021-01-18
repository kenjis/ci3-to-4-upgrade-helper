<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Validation\Validation;
use Config\Services;
use Config\Validation as ConfigValidation;
use Kenjis\CI3Compatible\Exception\NotImplementedException;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

use function is_array;

class CI_Form_validation
{
    /** @var Validation */
    private $validation;

    /** @var array */
    private $data;

    /** @var IncomingRequest */
    private $request;

    /**
     * @param array|ConfigValidation $config
     */
    public function __construct($config = null)
    {
        if (is_array($config)) {
            throw new NotSupportedException(
                'Setting validation rules with an array is not supported.'
                . ' Please convert it to `Config\Validation` class.'
                . ' See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#saving-sets-of-validation-rules-to-the-config-file>.'
            );
        }

        $this->validation = Services::validation($config);
        $this->validation->reset();

        $this->request = Services::request();
    }

    /**
     * Set Rules
     *
     * This function takes an array of field names and validation
     * rules as input, any custom error messages, validates the info,
     * and stores it
     *
     * @param   mixed  $field
     * @param   string $label
     * @param   mixed  $rules
     * @param   array  $errors
     *
     * @return  CI_Form_validation
     */
    public function set_rules(
        $field,
        ?string $label = null,
        $rules = null,
        array $errors = []
    ): CI_Form_validation {
        if ($this->request->getMethod() !== 'post') {
            return $this;
        }

        $this->validation->setRule($field, $label, $rules, $errors);

        return $this;
    }

    /**
     * For debugging
     *
     * @return Validation
     */
    public function getValidation(): Validation
    {
        return $this->validation;
    }

    public function set_data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set The Error Delimiter
     *
     * Permits a prefix/suffix to be added to each error message
     *
     * @param   string
     * @param   string
     *
     * @return  CI_Form_validation
     */
    public function set_error_delimiters($prefix = '<p>', $suffix = '</p>'): void
    {
        throw new NotSupportedException(
            'CI_Form_validation::set_error_delimiters() is not supported.'
            . ' Create custom views to display errors.'
            . ' See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#customizing-error-display>.'
        );
    }

    /**
     * Run the Validator
     *
     * This function does all the work.
     *
     * @param   string $config
     * @param   array  $data
     *
     * @return  bool
     */
    public function run(?string $config = null, ?array &$data = null): bool
    {
        if ($data !== null) {
            throw new NotImplementedException(
                '&$data is not implemented yet.'
            );
        }

        $this->setPostData();

        return $this->validation->run($this->data, $config);
    }

    protected function setPostData(): void
    {
        if ($this->data === null) {
            $this->set_data($this->request->getPost());
        }
    }
}
