<?php
return [
	####################
	# rule class mapping
	####################
	'rules_mapping' => [
		'ip_address' => Kit\Validator\Rule\IpAddress::class,
		'mac_address' => Kit\Validator\Rule\MacAddress::class,
		'email' => Kit\Validator\Rule\Email::class,
		'number' => Kit\Validator\Rule\Number::class,
		'required' => Kit\Validator\Rule\Required::class,
		'less_than' => Kit\Validator\Rule\LessThan::class,
		'max_length' => Kit\Validator\Rule\MaxLength::class,
		'integer' => Kit\Validator\Rule\Integer::class,
		'min_length' => Kit\Validator\Rule\MinLength::class,
		'greater_than' => Kit\Validator\Rule\GreaterThan::class,
		'directory' => Kit\Validator\Rule\Directory::class,
		'file' => Kit\Validator\Rule\File::class,
		'equals' => Kit\Validator\Rule\Equals::class,
		'alpha_num' => Kit\Validator\Rule\AlphaNum::class,
		'even' => Kit\Validator\Rule\Even::class,
		'odd' => Kit\Validator\Rule\Odd::class,
		'between' => Kit\Validator\Rule\Between::class,
	],

	######################
	# rules error messages
	######################
	'rules_error_messages' => [
		'required' => '%s field is required',
		'min_length' => '%s field must be at least %s characters',
		'max_length' => '%s field must not be more than %s characters',
		'less_than' => '%s field must not be less than %s',
		'greater_than' => '%s field must not be greaater than %s',
		'integer' => '%s field must be an integer',
		'number' => '%s field must be a valid number',
		'email' => '%s field must be a valid email address',
		'mac_address' => '%s field must be a valid mac address',
		'ip_address' => '%s field must be a valid ip address',
		'directory' => '%s is not a valid directory',
		'file' => '%s is not a valid file',
		'equals' => '%s must be the same with %s',
		'alpha_num' => '%s must be alpha numeric',
		'even' => '%s is not a valid even number',
		'odd' => '%s is not a valid odd number',
		'between' => '%s must be between %s and %s',
	]

];