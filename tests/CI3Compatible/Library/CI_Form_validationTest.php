<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;
use Kenjis\CI3Compatible\TestCase;

class CI_Form_validationTest extends TestCase
{
    public function test_setting_rules_with_array_is_not_supported(): void
    {
        $this->expectException(NotSupportedException::class);

        $rules = [];
        new CI_Form_validation($rules);
    }

    public function test_can_create_instance(): void
    {
        $validation = new CI_Form_validation();

        $this->assertInstanceOf(CI_Form_validation::class, $validation);
    }

    private function createFormValidation(): CI_Form_validation
    {
        $request = $this->getDouble(
            IncomingRequest::class,
            [
                'getMethod' => 'post',
                'getLocale' => 'en',
            ]
        );
        Services::injectMock('request', $request);

        return new CI_Form_validation();
    }

    public function test_set_rules(): void
    {
        $validation = $this->createFormValidation();

        $validation->set_rules('title', 'Title', 'required');

        $ci4Validation = $validation->getCI4Library();
        $rules = [
            'title' => [
                'label' => 'Title',
                'rules' => 'required',
            ],
        ];
        $this->assertSame($rules, $ci4Validation->getRules());
    }

    public function test_run_success(): void
    {
        $validation = $this->createFormValidation();

        $validation->set_rules('title', 'Title', 'required');

        $data = ['title' => 'News Title'];
        $ret = $validation->set_data($data)->run();

        $this->assertTrue($ret);
    }

    public function test_error_array(): void
    {
        $validation = $this->createFormValidation();

        $validation->set_rules('title', 'Title', 'required');

        $data = ['title' => ''];
        $ret = $validation->set_data($data)->run();

        $this->assertFalse($ret);

        $error_array = $validation->error_array();
        $this->assertEquals(
            'The Title field is required.',
            $error_array['title']
        );
    }

    public function test_error_string(): void
    {
        $validation = $this->createFormValidation();

        $this->expectException(NotSupportedException::class);

        $validation->error_string();
    }
}
