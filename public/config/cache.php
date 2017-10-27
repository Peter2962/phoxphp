<?php
######################################
# Application cache configuration file 
######################################
return [

	#################################
	# Option to enable cache or not #
	#################################
	'enabled' => 1,

	#################################
	# Cache driver namespace 		#
	#################################	
	'namespace' => 'Cache\\Driver\\',

	#################################
	# Cache driver 					#
	#################################
	'driver' => 'file',

	#################################
	# Cache driver interface 		#
	#################################
	'driver_interface' => 'Cache\\Interfaces\\DriverInterface',

	#################################
	# Cache duration period(in seconds)#
	#################################
	'duration' => 60,

	#################################
	# Cache storage location 		#
	#################################
	'storage' => 'storage/cache/'

];