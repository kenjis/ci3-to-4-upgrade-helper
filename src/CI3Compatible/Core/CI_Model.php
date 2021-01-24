<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core;

class CI_Model
{
    public function __construct()
    {
    }

    public function __get($key)
    {
        // Debugging note:
        //  If you're here because you're getting an error message
        //  saying 'Undefined Property: system/core/Model.php', it's
        //  most likely a typo in your model code.
        return get_instance()->$key;
    }
}
