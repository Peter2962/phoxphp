<?php
return [

	#############################
	# Base template path
	#############################
	'path' => dirname(__DIR__) . '/views',

	####################
	# Template extension
	####################
	'extension' => 'php',

	##########################################
	# If set to true, caching will be enabled.
	#########################################
	'enable_caching' => true,

	##############################
	# Cache configurations
	##############################
	'cache' => [
		// Where cached views will be saved
		'path' => dirname(__DIR__) . '/views/cache/',

		// Cache timeout
		'timeout' => 60
	],

	####################################################################
	# Should php syntax be allowed in template engine? Set this to false
	# if you do not want to allow php syntax.
	####################################################################
	'allow_default_syntax' => true,

	##############################################################################
	# Allowed system modules. If you do not want a module's mixin to be available
	# in the system, simply remove it or comment it out from in here.
	##############################################################################
	'system_modules' => [
		//...
		\Kit\PhoxEngine\Directives\Each::class,
		\Kit\PhoxEngine\Directives\Setter::class,
		\Kit\PhoxEngine\Directives\Getter::class,
		\Kit\PhoxEngine\Directives\_If::class,
		\Kit\PhoxEngine\Directives\_Else::class,
		\Kit\PhoxEngine\Directives\_ElseIf::class,
		\Kit\PhoxEngine\Directives\Raw::class,
		\Kit\PhoxEngine\Directives\Cookie::class,
		\Kit\PhoxEngine\Directives\_Include::class,
	]

];