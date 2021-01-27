<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

require __DIR__ . '/get_instance.php';
require __DIR__ . '/Common.php';

class CI_Controller extends BaseController
{
    /** @var CI_Controller */
    private static $instance;

    /** @var CI_Loader */
    public $load;

    public function __construct()
    {
        self::$instance =& $this;

        $this->load = new CI_Loader();
        $this->load->setController($this);

        $this->autoloadLibraries();
    }

    private function autoloadLibraries()
    {
        if (! isset($this->libraries)) {
            return;
        }

        foreach ($this->libraries as $library) {
            if ($library === 'database') {
                $this->load->database();

                continue;
            }

            $this->load->library($library);
        }
    }

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
    }

    public static function &get_instance(): CI_Controller
    {
        return self::$instance;
    }
}
