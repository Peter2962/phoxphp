<?php
include 'Exceptions/InvalidPackageFileException.php';
include 'Exceptions/PackageNotFoundException.php';
include 'Interfaces/PackagistInterface.php';
include 'Packagist.php';

$packagist = new Packagist(array(
	'display_errors' => 1,
	'package_file' 	 => appDir('package.json'),
	'log_file' 		 => appDir('storage/autoload/process.log')
));

$packagist->beginAutoload();