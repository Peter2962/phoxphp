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
		ApplicationCommand::class,
		Kit\Glider\Console\Command\Migration::class,
		Kit\Glider\Console\Command\Seed::class,
	],

	##############################
	# Sets migration files storage
	##############################
	'migrations_storage' => config('database')->get('migrations_storage'),

	##########################
	# Sets seeds files storage
	##########################
	'seeds_storage' => config('database')->get('seeds_storage')	
];