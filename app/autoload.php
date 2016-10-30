<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$classMap = array(
		'PHPExcel' => __DIR__.'/../vendor/phpexcel/Classes/PHPExcel.php'
);
$loader->addClassMap($classMap);

return $loader;
