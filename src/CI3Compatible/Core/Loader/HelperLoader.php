<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use function in_array;
use function is_string;

class HelperLoader
{
    /** @var string[] */
    private $autoloaded = ['url'];

    /** @var string[] */
    private $compatible = ['form'];

    /**
     * Helper Loader
     *
     * @param   string|string[] $helpers Helper name(s)
     *
     * @return  object
     */
    public function load($helpers): void
    {
        if (is_string($helpers)) {
            $helpers = [$helpers];
        }

        foreach ($helpers as $helper) {
            $this->loadOneHelper($helper);
        }
    }

    private function loadOneHelper(string $helper): void
    {
        if (in_array($helper, $this->autoloaded, true)) {
            return;
        }

        $this->loadCompatibleHelper($helper);
        $this->loadCI4Helper($helper);
    }

    /**
     * Load CI4 helper
     *
     * Returns bool for mocking this method.
     * Must be protected for mocking this method.
     */
    protected function loadCI4Helper(string $helper): bool
    {
        helper($helper);

        return true;
    }

    private function loadCompatibleHelper(string $helper): void
    {
        if (in_array($helper, $this->compatible, true)) {
            require __DIR__ . '/../../Helper/' . $helper . '_helper.php';
        }
    }
}
