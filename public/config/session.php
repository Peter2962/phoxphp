<?php
########################################
# Application session configuration file
########################################

return [
	##########################################
	# Session driver
	# ---------------------------------------
	# Native driver uses php's default session
	# functions.
	###########################################
	'driver' => 'native',

	#############################################
	# Session storage (For native session driver)
	#############################################
	'storage' => 'app/storage/session',

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