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

use Kenjis\CI3Compatible\Core\CI_Controller;

loadTestBootstrap();
new CI_Controller();

/*
 * -------------------------------------------------------------------
 *  Enabling Monkey Patching
 * -------------------------------------------------------------------
 *
 * If you want to use monkey patching, uncomment below code and configure
 * for your application.
 */
/*
use Kenjis\MonkeyPatch\Exception\ExitException;
use Kenjis\MonkeyPatch\MonkeyPatchManager;
use Kenjis\MonkeyPatch\Patcher\ConstantPatcher\Proxy as ConstProxy;
use Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy as FuncProxy;
use Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager;

const __GO_TO_ORIG__ = '__GO_TO_ORIG__';

class_alias(MonkeyPatchManager::class, 'MonkeyPatchManager');
class_alias(PatchManager::class, '__PatchManager__');
class_alias(ConstProxy::class, '__ConstProxy__');
class_alias(FuncProxy::class, '__FuncProxy__');

MonkeyPatchManager::init([
    // If you want debug log, set `debug` true, and optionally you can set the log file path
    'debug' => true,
    'log_file' => __DIR__ . '/../writable/logs/monkey-patch-debug.log',
    // PHP Parser: PREFER_PHP7, PREFER_PHP5, ONLY_PHP7, ONLY_PHP5
    'php_parser' => 'PREFER_PHP7',
    // Project root directory
    'root_dir' => __DIR__ . '/..',
    'cache_dir' => __DIR__ . '/../writable/cache/monkey-patch',
    // Directories to patch source files
    'include_paths' => [
        __DIR__ . '/../app',
        //__DIR__ . '/_support',
    ],
    // Excluding directories to patch
    'exclude_paths' => [
        __DIR__,
        // If you want to patch files inside paths below, you must add the directory starting with '-'
        //'-' . __DIR__ . '/_support',
    ],
    // All patchers you use
    'patcher_list' => [
         'ExitPatcher',
         'ConstantPatcher',
         'FunctionPatcher',
         'MethodPatcher',
    ],
    // Additional functions to patch
    'functions_to_patch' => [
        //'random_string',
    ],
    'exit_exception_classname' => ExitException::class,
]);
*/

function loadTestBootstrap()
{
    if (requireTestBootstrap() === false) {
        throw new RuntimeException(
            'Cannot find "system/Test/bootstrap.php" of CI4 in ' . __FILE__
        );
    }
}

function requireTestBootstrap()
{
    $testBootstraps = [
        __DIR__ . '/../../../../../codeigniter4/framework/system/Test/bootstrap.php',
        __DIR__ . '/../../../../../codeigniter4/codeigniter4/system/Test/bootstrap.php',
        __DIR__ . '/../codeigniter4/framework/system/Test/bootstrap.php',
        __DIR__ . '/../vendor/codeigniter4/codeigniter4/system/Test/bootstrap.php',
    ];

    foreach ($testBootstraps as $bootstrap) {
        if (file_exists($bootstrap)) {
            require $bootstrap;

            return true;
        }
    }

    return false;
}
