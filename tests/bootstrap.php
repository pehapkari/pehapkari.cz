<?php declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

require __DIR__ . '/../vendor/autoload.php';

// load envs
require_once __DIR__ . '/../config/bootstrap.php';

// for statie/tests - resp. symfony/validator
AnnotationRegistry::registerLoader('class_exists');
