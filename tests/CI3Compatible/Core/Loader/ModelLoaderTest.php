<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use CodeIgniter\Config\Factories;
use CodeIgniter\Model;
use Kenjis\CI3Compatible\TestSupport\TestCase;

class ModelLoaderTest extends TestCase
{
    /** @var News */
    private $controller;

    /** @var ModelLoader */
    private $loader;

    public function setUp(): void
    {
        $this->controller = new News();
        $injector = new ControllerPropertyInjector($this->controller);
        $this->loader = new ModelLoader($injector);

        $model = new Model();
        Factories::injectMock('model', 'App\Models\News_model', $model);
    }

    public function test_load_one_model(): void
    {
        $this->loader->load('news_model');

        $this->assertInstanceOf(
            'App\Models\News_model',
            $this->controller->news_model
        );
    }

    public function test_load_two_models(): void
    {
        $this->loader->load(['news_model', 'shop/shop_model']);

        $this->assertInstanceOf(
            'App\Models\News_model',
            $this->controller->news_model
        );
        $this->assertInstanceOf(
            'App\Models\Shop\Shop_model',
            $this->controller->shop_model
        );
    }

    public function test_load_one_model_twice(): void
    {
        $this->loader->load('news_model');
        $model1 = $this->controller->news_model;

        $this->loader->load('news_model');
        $model2 = $this->controller->news_model;

        $this->assertInstanceOf(
            'App\Models\News_model',
            $this->controller->news_model
        );
        $this->assertSame($model1, $model2);
    }

    public function test_load_one_model_two_instances(): void
    {
        $this->loader->load('news_model', 'a');
        $this->loader->load('news_model', 'b');

        $this->assertNotSame($this->controller->a, $this->controller->b);
        $this->assertInstanceOf(
            'App\Models\News_model',
            $this->controller->a
        );
        $this->assertInstanceOf(
            'App\Models\News_model',
            $this->controller->b
        );
    }
}
