<?php
return [

	#############################
	# Base template path
	#############################
	'path' => publicDir('views'),

	####################
	# Template extension
	####################
	'extension' => 'php',

	##########################################
	# If set to true, caching will be enabled.
	#########################################
	'enable_caching' => false,

	##############################
	# Cache directory
	##############################
	'cache_dir' => publicDir('views/cache/'),

	##############################################################################
	# Allowed system modules. If you do not want a module's mixin to be available
	# in the system, simply remove it or comment it out from in here.
	##############################################################################
	'system_modules' => [
		//...
		\Kit\PhoxEngine\Directives\Each::class,
		\Kit\PhoxEngine\Directives\Setter::class,
		\Kit\PhoxEngine\Directives\Getter::class,
		\Kit\PhoxEngine\Directives\_ElseIf::class,
		\Kit\PhoxEngine\Directives\_If::class,
		\Kit\PhoxEngine\Directives\_Else::class,
		\Kit\PhoxEngine\Directives\Raw::class,
		\Kit\PhoxEngine\Directives\Cookie::class,
		\Kit\PhoxEngine\Directives\_Include::class,
	],

	#####################
	# Filters class
	#####################
	'filterRepository' => App\View\Filters::class

];