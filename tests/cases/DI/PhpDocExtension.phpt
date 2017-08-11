<?php

/**
 * Test: DI\PhpDocExtension
 */

use Contributte\PhpDoc\DI\PhpDocExtension;
use Doctrine\Common\Annotations\Reader;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Reflection\ClassType;
use Tester\Assert;
use Tests\Fixtures\Annotation\Contributte;
use Tests\Fixtures\Service\FooService;

require_once __DIR__ . '/../../bootstrap.php';

// Load annotations
test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('phpdoc', new PhpDocExtension());
		$compiler->addConfig([
			'parameters' => [
				'tempDir' => TEMP_DIR,
				'debugMode' => TRUE,
			],
		]);
	}, 1);

	/** @var Container $container */
	$container = new $class();
	$container->initialize();

	/** @var Reader $reader */
	$reader = $container->getByType(Reader::class);
	$annotations = $reader->getClassAnnotations(ClassType::from(FooService::class));

	Assert::count(1, $annotations);
	Assert::type(Contributte::class, $annotations[0]);
	Assert::equal('phpdoc', $annotations[0]->package);
});
