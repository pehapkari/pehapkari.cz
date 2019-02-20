<?php declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

require __DIR__ . '/../vendor/autoload.php';

// for statie/tests - resp. symfony/validator
AnnotationRegistry::registerLoader('class_exists');
