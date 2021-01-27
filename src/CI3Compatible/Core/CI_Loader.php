<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use Config\Services;
use Kenjis\CI3Compatible\Core\Loader\ControllerPropertyInjector;
use Kenjis\CI3Compatible\Core\Loader\DatabaseLoader;
use Kenjis\CI3Compatible\Core\Loader\HelperLoader;
use Kenjis\CI3Compatible\Core\Loader\LibraryLoader;
use Kenjis\CI3Compatible\Core\Loader\ModelLoader;
use Kenjis\CI3Compatible\Database\CI_DB;

class CI_Loader
{
    /** @var CoreLoader */
    private $coreLoader;

    /** @var ModelLoader */
    private $modelLoader;

    /** @var DatabaseLoader */
    private $databaseLoader;

    /** @var HelperLoader */
    private $helperLoader;

    /** @var LibraryLoader */
    private $libraryLoader;

    public function __construct()
    {
        $this->coreLoader = CoreLoader::getInstance();

        $this->coreLoader->setLoader($this);
    }

    public function setController(CI_Controller $controller): void
    {
        $injector = new ControllerPropertyInjector($controller);

        $this->coreLoader->injectToController($injector);

        $this->loadLoaders($injector);
    }

    private function loadLoaders(ControllerPropertyInjector $injector): void
    {
        $this->modelLoader = new ModelLoader($injector);
        $this->databaseLoader = new DatabaseLoader($injector);
        $this->helperLoader = new HelperLoader();
        $this->libraryLoader = new LibraryLoader($injector);
    }

    /**
     * View Loader
     *
     * Loads "view" files.
     *
     * @param string $view   View name
     * @param array  $vars   An associative array of data
     *                   to be extracted for use in the view
     * @param bool   $return Whether to return the view output
     *                  or leave it to the Output class
     *
     * @return    object|string
     */
    public function view(string $view, array $vars = [], bool $return = false)
    {
        $this->injectLoadedClassesToView();

        if ($return) {
            return view($view, $vars);
        }

        echo view($view, $vars);
    }

    private function injectLoadedClassesToView(): void
    {
        $view = Services::renderer();
        $this->coreLoader->injectTo($view);
        $this->libraryLoader->injectTo($view);
        $this->modelLoader->injectTo($view);
    }

    /**
     * Model Loader
     *
     * Loads and instantiates models.
     *
     * @param mixed  $model   Model name
     * @param string $name    An optional object name to assign to
     * @param bool   $db_conn An optional database connection configuration to initialize
     *
     * @return  object
     */
    public function model(
        $model,
        string $name = '',
        bool $db_conn = false
    ): self {
        $this->modelLoader->load($model, $name, $db_conn);

        return $this;
    }

    /**
     * Helper Loader
     *
     * @param string|string[] $helpers Helper name(s)
     */
    public function helper($helpers = []): self
    {
        $this->helperLoader->load($helpers);

        return $this;
    }

    /**
     * Database Loader
     *
     * @param mixed $params        Database configuration options
     * @param bool  $return        Whether to return the database object
     * @param bool  $query_builder Whether to enable Query Builder
     *                     (overrides the configuration setting)
     *
     * @return    object|bool    Database object if $return is set to TRUE,
     *                    FALSE on failure, CI_Loader instance in any other case
     */
    public function database(
        $params = '',
        bool $return = false,
        ?bool $query_builder = null
    ) {
        $ret = $this->databaseLoader->load($params, $return, $query_builder);

        if ($ret === false) {
            return false;
        }

        if ($return && $ret instanceof CI_DB) {
            return $ret;
        }

        return $this;
    }

    /**
     * Library Loader
     *
     * Loads and instantiates libraries.
     * Designed to be called from application controllers.
     *
     * @param   mixed  $library     Library name
     * @param   array  $params      Optional parameters to pass to the library class constructor
     * @param   string $object_name An optional object name to assign to
     *
     * @return  object
     */
    public function library(
        $library,
        ?array $params = null,
        ?string $object_name = null
    ): self {
        $this->libraryLoader->load($library, $params, $object_name);

        return $this;
    }
}
