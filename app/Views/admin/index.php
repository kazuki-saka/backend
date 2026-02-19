<?php

use CodeIgniter\Boot;
use Config\Paths;

log_message('debug', "test0");
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

log_message('debug', "test1");
require '/home/fsdev/www/app/Config/Paths.php';
log_message('debug', "test2");


$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';
log_message('debug', "test3");

exit(Boot::bootWeb($paths));