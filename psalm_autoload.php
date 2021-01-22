<?php

declare(strict_types=1);

require __DIR__ . '/vendor/codeigniter4/codeigniter4/system/Test/bootstrap.php';

$helperDirs = [
    'vendor/codeigniter4/codeigniter4/system/Helpers',
    'src/CI3Compatible/Helper',
];

foreach ($helperDirs as $dir) {
    $dir = __DIR__ . '/' . $dir;
    chdir($dir);

    foreach (glob('*_helper.php') as $filename) {
        $filePath = realpath($dir . '/' . $filename);

        require_once $filePath;
    }
}
