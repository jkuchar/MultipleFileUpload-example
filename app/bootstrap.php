<?php

// Load Nette Framework or autoloader generated by Composer
require __DIR__ . '/../libs/autoload.php';


$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../libs')
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon', \Nette\Configurator::AUTO);
$configurator->addConfig(__DIR__ . '/config/config.local.neon', $configurator::NONE); // none section
$container = $configurator->createContainer();



// TODO: Move to separate file?
// <MultipleFileUploadControl>

// Setup MultipleFileUpload
MultipleFileUpload\MultipleFileUpload::register();

// Optional step: register custom user interfaces
// Registrator accepts instance of class or class name
// As defaults is used this:

//MultipleFileUpload\MultipleFileUpload::getUIRegistrator()
//	->clear() // removed default registered interfaces
//	
//	->register('MultipleFileUpload\UI\HTML4SingleUpload')
//	
//	and select one of these:
//	->register('MultipleFileUpload\UI\Uploadify');
//	->register('MultipleFileUpload\UI\SwfUpload');
//	->register('MultipleFileUpload\UI\Plupload');
//
//
//// If you want to use swfupload instead of uploadify use this setup code
//// Vyki is autor of swfupload extension: http://forum.nette.org/cs/profile.php?id=2221
//MultipleFileUpload\MultipleFileUpload::getUIRegistrator()
//	->clear()
//	->register('MultipleFileUpload\UI\HTML4SingleUpload')
//	->register('MultipleFileUpload\UI\Swfupload');
//
//// Or you can you uploadify
//MultipleFileUpload\MultipleFileUpload::getUIRegistrator()
//	->clear()
//	->register('MultipleFileUpload\UI\HTML4SingleUpload')
//	->register('MultipleFileUpload\UI\Uploadify');




//// Optional step: register driver
////
//// As default driver is used Sqlite driver
//// @see http://addons.nettephp.com/cs/multiplefileupload#toc-drivery
////
//// When you want to use other driver use something like this:
//
//dibi::connect(array(
//	"driver"   => "postgre",
//	"host"     => "127.0.0.1",
//	"dbname"   => "mfu",
//	"schema"   => "public",
//	"user"     => "postgres",
//	"pass"     => "toor",
//	"charset"  => "UTF-8"
//));
//MultipleFileUpload\MultipleFileUpload::setQueuesModel(new MultipleFileUpload\Model\Dibi\Queues());



//// Custom file validation function:
//// (this is used if none is specified)
//function validateMFUFile(\Nette\Http\FileUpload $file) {
//	return $file->isOk();
//}
//MultipleFileUpload::$validateFileCallback = callback('\validateMFUFile');

// </MultipleFileUploadControl>




return $container;
