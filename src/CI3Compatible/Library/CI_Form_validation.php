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
    private $validation_data;

    /** @var IncomingRequest */
    private $request;

    /**
     * Initialize Form_Validation class
     *
     * @param   ConfigValidation|array|null $rules
     *
     * @return  void
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
     *
     * @internal
     */
    public function getCI4Library(): Validation
    {
        return $this->validation;
    }

    public function set_data(array $data): self
    {
            $this->validation_data = $data;

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
     * Set Error Message
     *
     * Lets users set their own error messages on the fly. Note:
     * The key name has to match the function name that it corresponds to.
     *
     * @param   array
     * @param   string
     *
     * @return  CI_Form_validation
     */
    public function set_message($lang, $val = ''): void
    {
        throw new NotSupportedException(
            'set_message() is not supported.'
            . ' See <https://github.com/kenjis/ci3-to-4-migration-helper/blob/1.x/docs/HowToMigrateFromCI3ToCI4.md#form_validation>.'
        );
    }

    /**
     * CI4 method
     * Sets the error for a specific field. Used by custom validation methods.
     *
     * @param string $field
     * @param string $error
     *
     * @return CI_Form_validation
     */
    public function setError(string $field, string $error): self
    {
        $this->validation->setError($field, $error);

        return $this;
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

        return $this->validation->run($this->validation_data, $config);
    }

    protected function setPostData(): void
    {
        if ($this->validation_data === null) {
            $this->set_data($this->request->getPost());
        }
    }

    /**
     * Reset validation vars
     *
     * Prevents subsequent validation routines from being affected by the
     * results of any previous validation routine due to the CI singleton.
     *
     * @return  CI_Form_validation
     */
    public function reset_validation(): self
    {
        $this->validation->reset();
        $this->validation_data = null;

        return $this;
    }
}
