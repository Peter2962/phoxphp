<?php
############################
# Router configuration file.
############################
return [
	######################################
	# Throw error if route does not exist.
	######################################
	'throw_404_error' => true,

	################################################################
	# Exception class to use if `throw_404_error` is set to true and
	# route does not exist.
	################################################################
	'404_error_exception' => Kit\Http\Router\Exceptions\RouteNotFoundException::class,

	###########################################################
	# Setting this to true will allow us to be able to validate
	# route slugs.
	###########################################################
	'allow_slug_validation' => true,

	###################################
	# Configuration for slug validation
	###################################
	'slug_validation_options' => [
		'fallback_method_default_arguments' => [
			Kit\Http\Router\Factory::class
		]
	],

	####################
	# Controller filters
	####################
	'filters' => [
		'app' => AppFilter::class,
		'csrf' => Kit\Http\Router\Filters\CsrfTokenFilter::class
	],
];