<?php
##########################################
# application database configuration file.
##########################################

return [
	'mysql' => [
		'provider' => Kit\Glider\Platform\Mysqli\MysqliProvider::class,
		'host' => 'localhost',
		'alias' => 'mysqli',
		'username' => 'root',
		'password' => 'root',
		'database' => 'test',
		'charset' => 'utf8',
		'collation' => '',
		'domain' => ['localhost'],
		'auto_commit' => false,
		'prefix' => '',
		'alt' => null
	],
	'default' => [
		'provider' => Kit\Glider\Platform\Pdo\PdoProvider::class,
		'host' => 'localhost',
		'alias' => 'pdo',
		'username' => 'root',
		'password' => 'root',
		'database' => 'test',
		'charset' => 'utf8',
		'collation' => 'utf8',
		'domain' => ['localhost'],
		'prefix' => '',
		'auto_commit' => true,
		'alt' => null,
		'persistent' => true,
		'options' => [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_CASE => PDO::CASE_NATURAL,
			PDO::ATTR_PERSISTENT => true
		]
	]
];