<?php

use App\AppManager;

include 'app/Helper.php';

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$startTime = $time;

// Application initialization time.
define('APP_INIT_TIME', $startTime);

#################################
# Load packages via composer.
################################
include 'vendor/autoload.php';

#########################################
# Create new AppManager instance
# Note that an instance of the AppManager
# cannot be created again. If it is, an
# error will be thrown.
#########################################

$app = new AppManager();

$app->configure(function($config) {
	return array(
		'sys' => array(
			'date.timezone' => $config->get('app', 'timezone')
		),
		'env_push' => array('app', 'cache')
	);
}, AUTOLOADER);

##################################################################################
# We're booting our application now.
# The boot method accepts a boolean type parameter. The application will only boot
# if the boolean value is set to true.
##################################################################################

$app->boot(true);
