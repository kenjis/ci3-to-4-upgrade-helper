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

namespace Kenjis\CI3Compatible\Test\Traits;

use Config\Services;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CoreLoader;

use function get_instance;

trait ResetInstance
{
    /** @var string */
    protected $controller_class = 'App\Controllers\MY_Controller';

    /** @var CI_Controller */
    protected $CI;

    /**
     * Reset CodeIgniter instance and assign new CodeIgniter instance as $this->CI
     */
    public function resetInstance(bool $use_my_controller = false): void
    {
        new CoreLoader();

        $this->createCodeIgniterInstance($use_my_controller);
        $this->CI =& get_instance();
    }

    /**
     * Reset Services
     */
    public function resetServices(bool $initAutoloader = true): void
    {
        Services::reset($initAutoloader);

        // Reload routes
        require APPPATH . 'Config/Routes.php';
    }

    public function createCodeIgniterInstance(
        bool $use_my_controller = false
    ): CI_Controller {
        if ($use_my_controller) {
            return new $this->controller_class();
        }

        return new CI_Controller();
    }
}
