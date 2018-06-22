<?php
#####################################################################################
# Application services configuration file. All your services should be placed in here.
# Do not change the variable `$di` as this is being used by the core app object.
#####################################################################################

use App\Config;
use Kit\Auth\Auth;
use Kit\Http\Response;
use Kit\Prop\ClassLoader;
use Kit\Cache\CacheManager;
use Kit\Validator\Validator;
use Kit\Http\Request\RequestManager;
use Kit\View\Manager as ViewManager;
use Kit\Translation\Locale\LocaleManager;
use Kit\Translation\Factory as TranslationFactory;

$di->register('cache', CacheManager::class);
$di->register('config', Config::class);
$di->register('auth', Auth::class);
$di->register('request', RequestManager::class);
$di->register('session', Factory::class);
$di->register('response', Response::class)->setDefaultParameters(new RequestManager());
$di->register('en_msg', TranslationFactory::class)->setDefaultParameters(new LocaleManager('en', 'us'));
$di->register('loader', ClassLoader::class);
$di->register('validator', Validator::class);
$di->register('view', ViewManager::class)->setAction('getEngine');