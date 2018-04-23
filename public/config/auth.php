<?php
#######################
# Required table fields:
# email
# password
# session_token
# remember_token
# is_activated
# confirmation_code
# is_blocked
#######################
return [
	#################
	# auth users table
	#################
	'auth_users_table' => 'users',

	#########################
	# auth login session name
	#########################
	'auth_login_session_name' => 'logged_in',

	####################
	# auth connection id
	####################
	'connection_id' => 'default',

	########################################
	# set automatic login after registration
	########################################
	'auto_login' => false,

	#####################################
	# auth redirect url after logging out
	####################################
	'auth_logout_url' => '/',

	####################################
	# auth redirect url after logging in
	####################################
	'auth_login_url' => '/',

	############################################
	# set automatic redirection after logging in
	############################################
	'auto_redirect' => false,

	####################
	# auth cookie domain
	####################
	'cookie_domain' => null,

	##################
	# auth cookie path
	##################
	'cookie_path' => '/',

	##############################
	# auth remember cookie timeout
	##############################
	'cookie_alive_time' => time() + (86400 * 30),

	###########################################################
	# sets whether cookie should be set if connection is secure
	###########################################################
	'cookie_secure_connection' => false,

	##############################################################
	# sets whether to make the cookie accessible through http only
	##############################################################
	'cookie_http_only' => false,

	##############################################################
	# Sets whether to activate users automatically when registered
	##############################################################
	'auto_activate' => false,

	#####################################################################
	# Sets whether to check if user is activated before logging in or not
	#####################################################################
	'activation_check' => true,

	###############
	# auth messages
	###############
	'messages' => [
		'auth.login.user_not_found' => 'User does not exist.',
		'auth.login.blocked' => 'Acccount has been blocked.',
		'auth.login.not_activated' => 'Acccount is not activated.',
		'auth.activate.user_not_found' => 'User does not exist.',
		'auth.activate.user_activated' => 'Account already activated.',
		'auth.register.user_exists' => 'User already exists',
		'auth.block.empty_criteria' => 'Unable to block this account.',
		'auth.block.user_not_found' => 'User does not exist.',
		'auth.unblock.empty_criteria' => 'Unable to unblock this account.',
		'auth.unblock.user_not_found' => 'User does not exist.',
		'auth.delete.empty_criteria' => 'Unable to delete account.',
		'auth.login.incorrect_password' => 'Unable to login. Password is incorrect.',
		'auth.password.incorrect_password' => 'Password is incorrect.'
	]
];