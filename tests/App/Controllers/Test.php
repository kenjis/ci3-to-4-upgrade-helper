<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller;

class Test extends CI_Controller
{
    public function index(): void
    {
        echo __METHOD__;
    }

    public function redirect()
    {
        return redirect()->to('/');
    }
}
