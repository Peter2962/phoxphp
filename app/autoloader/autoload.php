<?php
include 'Exceptions/InvalidPackageFileException.php';
include 'Exceptions/PackageNotFoundException.php';
include 'Interfaces/PackagistInterface.php';
include 'Packagist.php';

$packagist = new Packagist(array(
	'display_errors' => 1,
	'package_file' 	 => 'app/package.json',
	'log_file' 		 => 'app/storage/autoload/process.log'
));

$packagist->beginAutoload();