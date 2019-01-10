<?php declare(strict_types=1);

use OpenTraining\OpenTrainingKernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

$possibleAutoloadFiles = [
    // project
    __DIR__  .'/../vendor/autoload.php',
    // monorepo
    __DIR__  .'/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadFiles as $possibleAutoloadFile) {
    if (file_exists($possibleAutoloadFile)) {
        require $possibleAutoloadFile;
    }
}

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new OpenTrainingKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
