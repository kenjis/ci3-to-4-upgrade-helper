<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use App\Controllers\News;
use CodeIgniter\Config\Factories;
use CodeIgniter\Model;
use Kenjis\CI3Compatible\TestCase;

class ModelLoaderTest extends TestCase
{
    public function test_load_model(): void
    {
        $controller = new News();
        $injector = new ControllerPropertyInjector($controller);
        $loader = new ModelLoader($injector);

        $model = new Model();
        Factories::injectMock('model', 'App\Models\News_model', $model);

        $loader->load('news_model');

        $this->assertInstanceOf(
            'App\Models\News_model',
            $controller->news_model
        );
    }
}
