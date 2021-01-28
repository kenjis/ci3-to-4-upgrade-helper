<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible;

use CodeIgniter\Test\TestLogger;
use Exception;

trait LogTestHelperTrait
{
    /**
     * Custom function to hook into CodeIgniter's Logging mechanism
     * to check if certain messages were logged during code execution.
     *
     * @param string      $level
     * @param string|null $expectedMessage
     *
     * @return bool
     *
     * @throws Exception
     */
    public function assertLogged(string $level, ?string $expectedMessage = null): bool
    {
        $result = TestLogger::didLog($level, $expectedMessage);

        $this->assertTrue(
            $result,
            '"' . $expectedMessage . '" is not logged.'
        );

        return $result;
    }
}
