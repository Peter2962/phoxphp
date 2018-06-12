<?php
return [
	######################################
	# Sets interface colour and background
	######################################
	'interface' => [
		'default' => [
			'text_color' => 'white',
			'text_background' => 'black'
		],
		'error' => [
			'text_color' => 'red',
			'text_background' => 'black'
		]
	],

	####################################################
	# Default path where runnable objects will be stored.
	####################################################
	'runnables_path' => publicDir('commands'),

	#######################
	# Runnables to register
	#######################
	'runnables' => [
		ApplicationCommand::class
	]
];