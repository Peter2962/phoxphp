<?php
#####################################################################################
# Application services configuration file. All your services should be placed in here.
# Do not change the variable {$app} as this is being used by the core app object.
#####################################################################################

$service->register('cache', Package\Cache\CacheManager::class);

$service->register('config', App\Config::class);

$service->register('request', Package\Http\Request\RequestManager::class);

$service->register('session', Package\Http\Session\Factory::class);

$service->register('response', Package\Http\Response::class)
->setDefaultParameters(new Package\Http\Request\RequestManager());

$service->register('en_msg', Package\Translation\Factory::class)
->setDefaultParameters(new Package\Translation\Locale\LocaleManager('en', 'us'));