<?php
return [
	#########################
	# Path to template files
	#########################
	'template_path' => 'public/views',

	######################################################################
	# Template files extension(Optional - when not using `html` extension)
	######################################################################
	'template_extension' => 'html',

	##########################################################
	# List of mixins to make available for template engine
	##########################################################
	'directives' => [
		'comment',
		'block',
		'container',
		'include',
		'if',
		'while',
		'elseif',
		'else',
		'output',
		'outputRaw',
		'init',
		'for',
		'each',
		'module',
		'function',
		'extend'
	],

	#######################################
	# Whether to allow php's `this` pointer
	#######################################
	'allow_default_pointer' => false,

	####################################
	# Modules path
	####################################
	'modules_path' => 'storage/modules/',

	#################################
	# Disable php syntax in templates
	#################################
	'disable_php_syntax' => true
];