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

	##################################
	# Disable certain session drivers
	##################################
	'disable_drivers' => array('')
];