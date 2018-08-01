<?php

use Kit\Glider\Schema\SchemaManager;
use Kit\Glider\Query\Builder\QueryBuilder;

class CreatesUsersTable
{
	
	/**
	* @access 	public
	* @return 	<void>
	*/
	public function up()
	{
		SchemaManager::table('users')->create(function($scheme) {
			$scheme->id('id');
			$scheme->varchar('email', 255);
			$scheme->varchar('password', 255);
			$scheme->varchar('session_token', 255);
			$scheme->varchar('remember_token', 255);
			$scheme->varchar('confirmation_code', 255);
			$scheme->integer('is_activated', 10, false, false, ['default' => 0, 'unsigned' => true]);
			$scheme->integer('is_blocked', 10, false, false, ['default' => 0, 'unsigned' => true]);
		});
	}

	/**
	* @access 	public
	* @return 	<void>
	*/
	public function down()
	{
		SchemaManager::table('users')->drop();
	}

}