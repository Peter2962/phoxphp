<?php
#########################################
# Base application configuration file.
#########################################

return [
	#########################
	# App url
	#########################
	'app_url' => 'http://phoxphp.site/',

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
	'devMode' => 'production',

	##################################
	# Message to return if the devMode
	# is set to production.
	##################################
	'production_error_message' => 'Site not available'
];