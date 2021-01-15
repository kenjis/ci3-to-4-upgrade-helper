<?php

declare(strict_types=1);

use Kenjis\CI3Compatible\Core\CI_Controller;

function &get_instance(): CI_Controller
{
    return CI_Controller::get_instance();
}
