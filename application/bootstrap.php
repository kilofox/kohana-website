<?php

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @see  http://kohana.top/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @see  http://kohana.top/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohana.top/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(['Kohana', 'auto_load']);

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init([
    'base_url' => '/',
    'index_file' => '',
]);

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules([
    //'auth'       => MODPATH.'auth',       // Basic authentication
    'cache' => MODPATH . 'cache', // Custom caching
    //'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    //'database'   => MODPATH.'database',   // Database access
    //'image'      => MODPATH.'image',      // Image manipulation
    //'oauth'      => MODPATH.'oauth',      // OAuth authentication
    //'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    //'pagination' => MODPATH.'pagination', // Paging of results
    //'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'kostache' => MODPATH . 'kostache', // Kostache templating
]);

/*
 * We want to show the world we're running on... Kohana of course!
 */
Kohana::$expose = TRUE;

/**
 * Cookie Salt
 * @see https://kohana.top/3.4/guide/kohana/cookies
 *
 * If you have not defined a cookie salt in your Cookie class then
 * uncomment the line below and define a preferrably long salt.
 */
 Cookie::$salt = 'null';

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('error', 'error')
    ->defaults([
        'controller' => 'home',
        'action' => 'error'
    ]);

Route::set('download', 'download')
    ->defaults([
        'controller' => 'download',
        'action' => 'index'
    ]);

Route::set('documentation', 'documentation')
    ->defaults([
        'controller' => 'documentation',
        'action' => 'index'
    ]);

Route::set('development', 'development')
    ->defaults([
        'controller' => 'development',
        'action' => 'index'
    ]);

Route::set('team', 'team')
    ->defaults([
        'controller' => 'team',
        'action' => 'index'
    ]);

Route::set('license', 'license')
    ->defaults([
        'controller' => 'license',
        'action' => 'index'
    ]);

Route::set('donate', 'donate')
    ->defaults([
        'controller' => 'donate',
        'action' => 'index'
    ]);

Route::set('home', '(index)')
    ->defaults([
        'controller' => 'home',
        'action' => 'index'
    ]);

//// Handles: feed/$type.rss and feed/$type.atom
//Route::set('feed', 'feed/<name>', ['name' => '.+'])
//    ->defaults([
//        'controller' => 'feed',
//        'action' => 'load',
//    ]);
//
//// Handles: download/$file
//Route::set('file', 'download/<file>', ['file' => '.+'])
//    ->defaults([
//        'controller' => 'file',
//        'action' => 'get',
//    ]);
//
//// Handles: donate
//Route::set('donate', 'donate(/<action>)')
//    ->defaults([
//        'controller' => 'donate',
//        'action' => 'index',
//    ]);
//
//// Handles: $lang/$page and $page
//Route::set('page', '((<lang>/)<page>)', ['lang' => '[a-z]{2}', 'page' => '.+'])
//    ->defaults([
//        'controller' => 'page',
//        'action' => 'load',
//    ]);
