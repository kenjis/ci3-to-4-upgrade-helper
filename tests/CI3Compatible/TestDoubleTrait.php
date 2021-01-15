<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use Closure;
use PHPUnit\Framework\MockObject\Stub\Stub;

use function array_keys;
use function array_merge;
use function call_user_func_array;
use function is_array;
use function is_int;
use function is_object;

trait TestDoubleTrait
{
    /**
     * Get Mock Object
     *
     * $email = $this->getMockBuilder('CI_Email')
     *    ->disableOriginalConstructor()
     *    ->setMethods(['send'])
     *    ->getMock();
     * $email->method('send')->willReturn(TRUE);
     *
     *  will be
     *
     * $email = $this->getDouble('CI_Email', ['send' => TRUE]);
     *
     * @param array $params             [method_name => return_value]
     * @param mixed $constructor_params false: disable constructor, array: constructor params
     *
     * @return mixed PHPUnit mock object
     */
    public function getDouble(string $classname, array $params, $constructor_params = false)
    {
        // `disableOriginalConstructor()` is the default, because if we call
        // constructor, it may call `$this->load->...` or other CodeIgniter
        // methods in it. But we can't use them in
        // `$this->request->setCallablePreConstructor()`
        $mockBuilder = $this->getMockBuilder($classname);
        if ($constructor_params === false) {
            $mockBuilder->disableOriginalConstructor();
        } elseif (is_array($constructor_params)) {
            $mockBuilder->setConstructorArgs($constructor_params);
        }

        $methods = [];
        $onConsecutiveCalls = [];
        $otherCalls = [];

        foreach ($params as $key => $val) {
            if (is_int($key)) {
                $onConsecutiveCalls = array_merge($onConsecutiveCalls, $val);
                $methods[] = array_keys($val)[0];
            } else {
                $otherCalls[$key] = $val;
                $methods[] = $key;
            }
        }

        $mock = $mockBuilder->setMethods($methods)->getMock();

        foreach ($onConsecutiveCalls as $method => $returns) {
            $mock->expects($this->any())->method($method)
                ->will(
                    call_user_func_array(
                        [$this, 'onConsecutiveCalls'],
                        $returns
                    )
                );
        }

        foreach ($otherCalls as $method => $return) {
            if (
                is_object(
                    $return
                ) && ($return instanceof Stub)
            ) {
                $mock->expects($this->any())->method($method)
                    ->will($return);
            } elseif (is_object($return) && $return instanceof Closure) {
                $mock->expects($this->any())->method($method)
                    ->willReturnCallback($return);
            } else {
                $mock->expects($this->any())->method($method)
                    ->willReturn($return);
            }
        }

        return $mock;
    }

    protected function _verify($mock, $method, $params, $expects, $with): void
    {
        $invocation = $mock->expects($expects)->method($method);

        if ($params === null) {
            return;
        }

        call_user_func_array([$invocation, $with], $params);
    }

    /**
     * Verifies that method was called exactly $times times
     *
     * $loader->expects($this->exactly(2))
     *    ->method('view')
     *    ->withConsecutive(
     *        ['shop_confirm', $this->anything(), TRUE],
     *        ['shop_tmpl_checkout', $this->anything()]
     *    );
     *
     *  will be
     *
     * $this->verifyInvokedMultipleTimes(
     *    $loader,
     *    'view',
     *    2,
     *    [
     *        ['shop_confirm', $this->anything(), TRUE],
     *        ['shop_tmpl_checkout', $this->anything()]
     *    ]
     * );
     *
     * @param mixed $mock   PHPUnit mock object
     * @param array $params arguments
     */
    public function verifyInvokedMultipleTimes(
        $mock,
        string $method,
        int $times,
        ?array $params = null
    ): void {
        $this->_verify(
            $mock,
            $method,
            $params,
            $this->exactly($times),
            'withConsecutive'
        );
    }

    /**
     * Verifies a method was invoked at least once
     *
     * @param mixed $mock   PHPUnit mock object
     * @param array $params arguments
     */
    public function verifyInvoked($mock, string $method, ?array $params = null): void
    {
        $this->_verify(
            $mock,
            $method,
            $params,
            $this->atLeastOnce(),
            'with'
        );
    }

    /**
     * Verifies that method was invoked only once
     *
     * @param mixed $mock   PHPUnit mock object
     * @param array $params arguments
     */
    public function verifyInvokedOnce($mock, string $method, ?array $params = null): void
    {
        $this->_verify(
            $mock,
            $method,
            $params,
            $this->once(),
            'with'
        );
    }

    /**
     * Verifies that method was not called
     *
     * @param mixed $mock   PHPUnit mock object
     * @param array $params arguments
     */
    public function verifyNeverInvoked($mock, string $method, ?array $params = null): void
    {
        $this->_verify(
            $mock,
            $method,
            $params,
            $this->never(),
            'with'
        );
    }
}
