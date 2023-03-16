<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

namespace Kenjis\CI3Compatible\Core;

use Config\Services;
use Kenjis\CI3Compatible\Core\Loader\ControllerPropertyInjector;
use Kenjis\CI3Compatible\Core\Loader\DatabaseLoader;
use Kenjis\CI3Compatible\Core\Loader\HelperLoader;
use Kenjis\CI3Compatible\Core\Loader\LibraryLoader;
use Kenjis\CI3Compatible\Core\Loader\ModelLoader;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_forge;
use Kenjis\CI3Compatible\Exception\NotImplementedException;

use function array_keys;
use function array_merge;
use function assert;
use function end;
use function explode;
use function extract;
use function file_exists;
use function file_get_contents;
use function get_instance;
use function get_object_vars;
use function ini_get;
use function is_array;
use function is_object;
use function is_string;
use function ob_end_clean;
use function ob_end_flush;
use function ob_get_contents;
use function ob_get_level;
use function ob_start;
use function pathinfo;
use function preg_replace;
use function show_error;
use function str_replace;
use function strncmp;

use const PATHINFO_EXTENSION;

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

    /** @var bool */
    private $coreClassesInjectedToView = false;

    public function __construct()
    {
        $this->coreLoader = CoreLoader::getInstance();

        $this->coreLoader->setLoader($this);
    }

    public function setController(CI_Controller $controller): void
    {
        assert(
            is_object($this->coreLoader),
            '$coreLoader is not set.'
            . ' Please call CI_Loader::__construct().'
        );

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
     * @return string
     */
    public function view_(string $view, array $vars = [], bool $return = false)
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

        if ($this->coreClassesInjectedToView === false) {
            $this->coreLoader->injectTo($view);
        }

        $this->libraryLoader->injectTo($view);
        $this->modelLoader->injectTo($view);

        $this->coreClassesInjectedToView = true;
    }

    /**
     * View Loader
     *
     * Loads "view" files.
     *
     * @param   string $view   View name
     * @param   array  $vars   An associative array of data
     *             to be extracted for use in the view
     * @param   bool   $return Whether to return the view output
     *             or leave it to the Output class
     *
     * @return  object|string
     */
    public function view(string $view, array $vars = [], bool $return = false)
    {
        return $this->_ci_load([
            '_ci_view' => $view,
            '_ci_vars' => $this->_ci_prepare_view_vars($vars),
            '_ci_return' => $return,
        ]);
    }

    /**
     * Internal CI Data Loader
     *
     * Used to load views and files.
     *
     * Variables are prefixed with _ci_ to avoid symbol collision with
     * variables made available to view files.
     *
     * @param   array $_ci_data Data to load
     *
     * @return  object
     *
     * @used-by CI_Loader::view()
     * @used-by CI_Loader::file()
     */
    protected function _ci_load(array $_ci_data)
    {
        // Set the default data variables
        foreach (['_ci_view', '_ci_vars', '_ci_path', '_ci_return'] as $_ci_val) {
            $$_ci_val = $_ci_data[$_ci_val] ?? false;
        }

        $file_exists = false;

        // Set the path to the requested file
        if (is_string($_ci_path) && $_ci_path !== '') {
            $_ci_x = explode('/', $_ci_path);
            $_ci_file = end($_ci_x);
        } else {
            $_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
            $_ci_file = $_ci_ext === '' ? $_ci_view . '.php' : $_ci_view;

            foreach ($this->_ci_view_paths as $_ci_view_file => $cascade) {
                if (file_exists($_ci_view_file . $_ci_file)) {
                    $_ci_path = $_ci_view_file . $_ci_file;
                    $file_exists = true;
                    break;
                }

                if (! $cascade) {
                    break;
                }
            }
        }

        if (! $file_exists && ! file_exists($_ci_path)) {
            show_error('Unable to load the requested file: ' . $_ci_file);
        }

        // This allows anything loaded using $this->load (views, files, etc.)
        // to become accessible from within the Controller and Model functions.
        $_ci_CI =& get_instance();
        foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
            if (! isset($this->$_ci_key)) {
                $this->$_ci_key =& $_ci_CI->$_ci_key;
            }
        }

        /*
         * Extract and cache variables
         *
         * You can either set variables using the dedicated $this->load->vars()
         * function or via the second parameter of this function. We'll merge
         * the two types and cache them so that views that are embedded within
         * other views can have access to these variables.
         */
        empty($_ci_vars) or $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
        extract($this->_ci_cached_vars);

        /*
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be post-processed by
         *  the output class. Why do we need post processing? For one thing,
         *  in order to show the elapsed page load time. Unless we can
         *  intercept the content right before it's sent to the browser and
         *  then stop the timer it won't be accurate.
         */
        ob_start();

        // If the PHP installation does not support short tags we'll
        // do a little string replacement, changing the short tags
        // to standard PHP echo statements.
        if (! is_php('5.4') && ! ini_get('short_open_tag') && config_item('rewrite_short_tags') === true) {
            echo eval('?>' . preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
        } else {
            include $_ci_path; // include() vs include_once() allows for multiple views with the same name
        }

        log_message('info', 'File loaded: ' . $_ci_path);

        // Return the file data if requested
        if ($_ci_return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();

            return $buffer;
        }

        /*
         * Flush the buffer... or buff the flusher?
         *
         * In order to permit views to be nested within
         * other views, we need to flush the content back out whenever
         * we are beyond the first level of output buffering so that
         * it can be seen and included properly by the first included
         * template and any subsequent ones. Oy!
         */
        if (ob_get_level() > $this->_ci_ob_level + 1) {
            ob_end_flush();
        } else {
            $_ci_CI->output->append_output(ob_get_contents());
            @ob_end_clean();
        }

        return $this;
    }

    /**
     * Prepare variables for _ci_vars, to be later extract()-ed inside views
     *
     * Converts objects to associative arrays and filters-out internal
     * variable names (i.e. keys prefixed with '_ci_').
     *
     * @param   mixed $vars
     *
     * @return  array
     */
    protected function _ci_prepare_view_vars($vars)
    {
        if (! is_array($vars)) {
            $vars = is_object($vars) ? get_object_vars($vars) : [];
        }

        foreach (array_keys($vars) as $key) {
            if (strncmp($key, '_ci_', 4) === 0) {
                unset($vars[$key]);
            }
        }

        return $vars;
    }

    /**
     * Model Loader
     *
     * Loads and instantiates models.
     *
     * @param mixed             $model   Model name
     * @param string            $name    An optional object name to assign to
     * @param bool|string|array $db_conn An optional database connection configuration to initialize
     *
     * @return  object
     */
    public function model(
        $model,
        string $name = '',
        $db_conn = false
    ): self {
        assert(
            is_object($this->modelLoader),
            'Controller is not set.'
            . ' Please call CI_Loader::setController() before calling database().'
        );

        if ($db_conn !== false) {
            if ($db_conn === true) {
                $db_conn = '';
            }

            $this->database($db_conn, false, true);
        }

        $this->modelLoader->load($model, $name);

        return $this;
    }

    /**
     * Helper Loader
     *
     * @param string|string[] $helpers Helper name(s)
     */
    public function helper($helpers = []): self
    {
        assert(
            is_object($this->helperLoader),
            'Controller is not set.'
            . ' Please call CI_Loader::setController() before calling helper().'
        );

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
        assert(
            is_object($this->databaseLoader),
            'Controller is not set.'
            . ' Please call CI_Loader::setController() before calling database().'
        );

        if ($params === '') {
            $params = null;
        }

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
     * Load the Database Forge Class
     *
     * @param   object $db     Database object
     * @param   bool   $return Whether to return the DB Forge class object or not
     *
     * @return  object
     */
    public function dbforge(?object $db = null, bool $return = false)
    {
        assert(
            is_object($this->databaseLoader),
            'Controller is not set.'
            . ' Please call CI_Loader::setController() before calling database().'
        );

        if ($db !== null) {
            throw new NotImplementedException(
                '$db is not implemented yet.'
            );
        }

        $ret = $this->databaseLoader->loadDbForge($db, $return);

        if ($return && $ret instanceof CI_DB_forge) {
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
        assert(
            is_object($this->libraryLoader),
            'Controller is not set.'
            . ' Please call CI_Loader::setController() before calling database().'
        );

        $this->libraryLoader->load($library, $params, $object_name);

        return $this;
    }
}
