<?php
##########################################
# application database configuration file.
# Note: PDO not available.
##########################################

return [
	'default' => [
		'provider' => Kit\Glider\Platform\Mysqli\MysqliProvider::class,
		'host' => 'localhost',
		'alias' => 'mysqli',
		'username' => 'root',
		'password' => 'root',
		'database' => 'test',
		'charset' => 'utf8',
		'collation' => '',
		'domain' => 'example.com',
		'auto_commit' => false,
		'alt' => 'dev'
	],
	'dev' => [		'provider' => Kit\Glider\Platform\Pdo\PdoProvider::class,
		'host' => 'localhost',
		'alias' => 'pdo',
		'username' => 'root',
		'password' => 'root',
		'database' => 'test',
		'charset' => 'utf8',
		'collation' => 'utf8',
		'domain' => 'http://server.web/',
		'auto_commit' => true,
		'alt' => null
	]
];