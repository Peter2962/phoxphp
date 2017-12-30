<?php
#####################################################################################
# Application services configuration file. All your services should be placed in here.
# Do not change the variable {$app} as this is being used by the core app object.
#####################################################################################

$service->register('cache', Kit\Cache\CacheManager::class);

$service->register('config', App\Config::class);

$service->register('request', Kit\Http\Request\RequestManager::class);

$service->register('session', Kit\Http\Session\Factory::class);

$service->register('response', Kit\Http\Response::class)
->setDefaultParameters(new Kit\Http\Request\RequestManager());

$service->register('en_msg', Kit\Translation\Factory::class)
->setDefaultParameters(new Kit\Translation\Locale\LocaleManager('en', 'us'));