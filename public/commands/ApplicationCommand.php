<?php

use Kit\Console\Command;
use Kit\Console\Environment;
use App\CommandTemplateParser;
use Kit\Console\Contract\Runnable;

class ApplicationCommand implements Runnable
{
	
	/**
	* @var 		$env
	* @access 	protected
	*/
	protected	$env;

	/**
	* @var 		$cmd
	* @access 	protected
	*/
	protected	$cmd;

	/**
	* @var 		$tags
	* @access 	protected
	*/
	protected	$tags = [
		'[phx:username]' => '',
		'[phx:host]' => '',
		'[phx:password]' => '',
		'[phx:database]' => '',
		'[phx:domain]' => ''
	];

	/**
	* {@inheritDoc}
	*/
	public function __construct(Environment $env, Command $cmd)
	{
		$this->env = $env;
		$this->cmd = $cmd;
	}

	/**
	* {@inheritDoc}
	*/
	public function getId() : String
	{
		return 'app';
	}

	/**
	* {@inheritDoc}
	*/
	public function run(Array $argumentsList, int $argumentsCount)
	{
		$template = file_get_contents(appDir('templates/commands/db.config'));

		// database host
		$dbhost = $this->cmd->question('1. database host?');
		$this->tags['[phx:host]'] = trim($dbhost);

		// database name
		$dbname = $this->cmd->question('2. database name?');
		$this->tags['[phx:database]'] = trim($dbname);

		// database username
		$dbusername = $this->cmd->question('3. database username?');
		$this->tags['[phx:username]'] = trim($dbusername);

		// database password
		$dbpassword = $this->cmd->question('4. database password?');
		$this->tags['[phx:password]'] = trim($dbpassword);

		// database domain
		$domain = $this->cmd->question('5. On which domain should this configuration be accessible?');
		$this->tags['[phx:domain]'] = trim($domain);

		CommandTemplateParser::checkTags(array_keys($this->tags), $template);
		$content = str_replace(
			array_keys($this->tags),
			array_values($this->tags),
			$template
		);

		$originalDbConfigFile = publicDir('config/database.php');
		$handle = fopen($originalDbConfigFile, 'w');
		fwrite($handle, $content);
		fclose($handle);

		$this->env->sendOutput('Database has been successfully configured', 'green', 'black');
	}

	/**
	* {@inheritDoc}
	*/
	public function runnableCommands() : Array
	{
		return [
			"db-config" => ":none",
		];
	}

}