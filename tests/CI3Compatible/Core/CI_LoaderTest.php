<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use App\Libraries\Validation\Field_validation;
use App\Models\News_model;
use App\Models\Shop\Shop_model;
use CodeIgniter\Config\Factories;
use Config\Paths;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_forge;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Library\CI_User_agent;
use Kenjis\CI3Compatible\TestSupport\TestCase;

use function ob_get_clean;
use function ob_start;
use function property_exists;

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

    public function test_view_output_return_false(): void
    {
        ob_start();
        $this->loader->view('welcome_message', []);
        $output = ob_get_clean();

        $this->assertStringContainsString(
            '<title>Welcome to CodeIgniter 4!</title>',
            $output
        );
    }

    public function test_view_nested(): void
    {
        $pathConfig = config(Paths::class);
        $pathConfig->viewDirectory = __DIR__ . '/../../App/Views/';
        Factories::injectMock('config', 'Paths', $pathConfig);
        $this->loader = new CI_Loader();
        $this->controller = new CI_Controller();
        $this->loader->setController($this->controller);

        $output = $this->loader->view('layout', [], true);

        $this->assertStringContainsString(
            '<p>This is layout header.</p>
<p>This is body.</p>
<p>This is layout footer.</p>',
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

    public function test_model_loads_Shop_model_in_sub_dir(): void
    {
        $ret = $this->loader->model('shop/shop_model');

        $this->assertInstanceOf(
            Shop_model::class,
            $this->controller->shop_model
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_model_loads_model_only(): void
    {
        $ret = $this->loader->model('shop/shop_model');

        $this->assertInstanceOf(
            Shop_model::class,
            $this->controller->shop_model
        );
        $this->assertFalse(isset($this->controller->db));
    }

    public function test_model_loads_model_and_db(): void
    {
        $ret = $this->loader->model(
            'shop/shop_model',
            '',
            true
        );

        $this->assertInstanceOf(
            CI_DB::class,
            $this->controller->db
        );
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

        $this->assertFalse(property_exists($this->controller, 'db'));
        $this->assertInstanceOf(CI_DB::class, $ret);
    }

    public function test_dbforge_loads_CI_DB_forge_and_returns_CI_Loader(): void
    {
        $ret = $this->loader->dbforge();

        $this->assertInstanceOf(
            CI_DB_forge::class,
            $this->controller->dbforge
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_dbforge_loads_CI_DB_dbforge_and_returns_CI_DB_dbforge(): void
    {
        $ret = $this->loader->dbforge(null, true);

        $this->assertFalse(property_exists($this->controller, 'dbforge'));
        $this->assertInstanceOf(CI_DB_forge::class, $ret);
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

    public function test_library_loads_CI_User_agent_and_returns_CI_Loader(): void
    {
        $ret = $this->loader->library('user_agent');

        $this->assertInstanceOf(
            CI_User_agent::class,
            $this->controller->agent
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_library_loads_two_libraries(): void
    {
        $ret = $this->loader->library(['form_validation', 'user_agent']);

        $this->assertInstanceOf(
            CI_User_agent::class,
            $this->controller->agent
        );
        $this->assertInstanceOf(
            CI_Form_validation::class,
            $this->controller->form_validation
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }

    public function test_library_loads_Field_validation_in_sub_dir(): void
    {
        $ret = $this->loader->library('validation/field_validation');

        $this->assertInstanceOf(
            Field_validation::class,
            $this->controller->field_validation
        );
        $this->assertInstanceOf(CI_Loader::class, $ret);
    }
}
