<?php
#####################################################################################
# Application services configuration file. All your services should be placed in here.
# Do not change the variable `$di` as this is being used by the core app object.
#####################################################################################

$di->register('cache', Kit\Cache\CacheManager::class);

$di->register('config', App\Config::class);

$di->register('auth', Kit\Auth\Auth::class);

$di->register('request', Kit\Http\Request\RequestManager::class);

$di->register('session', Kit\Http\Session\Factory::class);

$di->register('response', Kit\Http\Response::class)
->setDefaultParameters(new Kit\Http\Request\RequestManager());

$di->register('en_msg', Kit\Translation\Factory::class)
->setDefaultParameters(new Kit\Translation\Locale\LocaleManager('en', 'us'));

$di->register('loader', Kit\Prop\ClassLoader::class);

$di->register('validator', Kit\Validator\Validator::class);