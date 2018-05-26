<?php
########################################
# Application session configuration file
########################################

return [
	##########################################
	# Session driver
	# ---------------------------------------
	# Set default session driver to use. 
	###########################################
	'driver' => \Kit\Http\Session\Drivers\File\FileDriver::class,

	#############################################
	# Session storage (For native session driver)
	#############################################
	'storage' => appDir('storage/session'),

	###################################################
	# Session timeout
	# --------------------------------------------------
	# Default timeout for a sesison is 60 seconds.
	# Each session driver has two special methods
	# that can be used to increase session duration and
	# also decrease the session duration. They are: 
	# {incrementTimeout & decrementTimeout}.
	###################################################
	'timeout' => 90,

	#######################################################
	# Csrf token input name. This will be the name of your
	# input name that will contain the generated csrf token.  
	########################################################
	'csrf_token_input_name' => 'token',

	##################################
	# Disable certain session drivers
	##################################
	'disable_drivers' => array('')
];