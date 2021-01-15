<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use App\Models\News_model;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\TestCase;

class CI_LoaderTest extends TestCase
{
    /** @var CI_Controller */
    private $controller;

    /** @var CI_Loader */
    private $loader;

    public function setUp(): void
    {
        parent::setUp();

        $coreLoader = new CoreLoader();
        $this->loader = new CI_Loader();
        $this->controller = new CI_Controller();
        $this->loader->setController($this->controller);
    }

    public function test_controller_has_core_classes(): void
    {
        $this->assertInstanceOf(
            CI_Loader::class,
            $this->controller->load
        );
        $this->assertInstanceOf(
            CI_Input::class,
            $this->controller->input
        );
    }

    public function test_view_returns_output(): void
    {
        $output = $this->loader->view('welcome_message', [], true);

        $this->assertStringContainsString(
            '<title>Welcome to CodeIgniter 4!</title>',
            $output
        );
    }

    public function test_model_loads_News_model(): void
    {
        $ret = $this->loader->model('news_model');

        $this->assertInstanceOf(
            News_model::class,
            $this->controller->news_model
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_database_loads_CI_DB_and_returns_CI_Loader(): void
    {
        $ret = $this->loader->database();

        $this->assertInstanceOf(
            CI_DB::class,
            $this->controller->db
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_database_loads_CI_DB_and_returns_CI_DB(): void
    {
        $ret = $this->loader->database('', true);

        $this->assertInstanceOf(
            CI_DB::class,
            $this->controller->db
        );
        $this->assertInstanceOf(CI_DB::class, $ret);
    }

    public function test_library_loads_CI_Form_validation_and_returns_CI_Loader(): void
    {
        $ret = $this->loader->library('form_validation');

        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->form_validation
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }
}
