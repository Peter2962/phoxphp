<?php
#########################################
# Base application configuration file.
#########################################

return [
	#########################
	# App url
	#########################
	'app_url' => '',

	#########################
	# App name
	#########################
	'app_name' => '',

	#########################
	# Routes file path
	#########################
	'app_routes' => 'public/config/routes.php',

	#########################
	# Services file path
	#########################
	'app_services' => 'public/config/services.php',

	#########################
	# Default layout
	#########################
	'default_layout' => 'default',

	#########################
	# App timezone
	#########################
	'timezone' => 'Africa/Abidjan',

	################################################################
	# Set mode your application should run in.
	# Few parts of the framework reacts differently to the mode set.
	# Accepts [dev] or [production]
	################################################################
	'devMode' => 'dev',

	##################################
	# Message to return if the devMode
	# is set to production.
	##################################
	'production_error_message' => 'Site not available',

	#######################
	# Storage for log files
	#######################
	'log_path' => 'app/storage/log/',

	####################################################
	# random string key used in randomString function
	####################################################
	'random_string_key' => '$.1.al10123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$!@'
];