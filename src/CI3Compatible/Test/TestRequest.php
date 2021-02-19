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

namespace Kenjis\CI3Compatible\Test;

use CodeIgniter\Test\FeatureResponse;
use CodeIgniter\Test\FeatureTestCase;
use Kenjis\CI3Compatible\Test\TestCase\TestCase;

use function get_instance;
use function strtolower;

class TestRequest
{
    /** @var TestRequest */
    private static $instance;

    /** @var TestCase */
    private $testCase;

    /** @var FeatureResponse */
    private $result;

    /** @var callable[] callable called post controller constructor */
    private $callables = [];

    public function __construct(FeatureTestCase $testCase)
    {
        self::$instance = $this;

        $this->testCase = $testCase;

        $this->initGlobalSession();
    }

    private function initGlobalSession()
    {
        $_SESSION = $_SESSION ?? [];
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    /**
     * Request to Controller
     *
     * @param string       $httpMethod HTTP method
     * @param array|string $argv       array of controller,method,arg|uri
     * @param array|string $params     POST params/GET params|raw_input_stream
     */
    public function request(string $httpMethod, $argv, $params = []): string
    {
        $this->result = $this->testCase->withSession($_SESSION)->call(
            strtolower($httpMethod),
            $argv,
            $params
        );

        return $this->result->response->getBody();
    }

    /**
     * Set (and Reset) callable
     *
     * @param callable $callable function to run after controller instantiation
     */
    public function setCallable(callable $callable): void
    {
        $this->callables = [];
        $this->callables[] = $callable;
    }

    /**
     * Add callable
     *
     * @param callable $callable function to run after controller instantiation
     */
    public function addCallable(callable $callable): void
    {
        $this->callables[] = $callable;
    }

    public function runCallables(): void
    {
        $CI = get_instance();

        if ($this->callables === []) {
            return;
        }

        foreach ($this->callables as $callable) {
            $callable($CI);
        }
    }

    /**
     * Asserts Redirect
     *
     * @param string $uri  URI to redirect
     * @param int    $code response code
     */
    public function assertRedirect(string $uri, ?int $code = null): void
    {
        // @TODO check URI
        $uri;
        $this->result->assertRedirect();

        if ($code === null) {
            return;
        }

        $this->result->assertStatus($code);
    }
}
