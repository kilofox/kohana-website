<?php

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link https://kohana.top/guide/about.install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link http://www.php.net/manual/errorfunc.configuration#ini.error-reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and warnings. Enable them by using: E_ALL
 *
 * In a production environment, it is safe to ignore notices and warnings.
 * Disable them by using: E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @link https://kohana.top/guide/using.configuration
 */
// Set the full path to the docroot
define('DOCROOT', __DIR__ . DIRECTORY_SEPARATOR);

// Define the absolute paths for required directories
define('APPPATH', realpath(DOCROOT . '3.1/application') . DIRECTORY_SEPARATOR);
define('MODPATH', realpath(DOCROOT . '3.1/modules') . DIRECTORY_SEPARATOR);
define('SYSPATH', realpath(DOCROOT . '3.1/system') . DIRECTORY_SEPARATOR);

/**
 * Define the start time of the application, used for profiling.
 */
if (!defined('KOHANA_START_TIME')) {
    define('KOHANA_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if (!defined('KOHANA_START_MEMORY')) {
    define('KOHANA_START_MEMORY', memory_get_usage());
}

// Bootstrap the application
require '3.1-bootstrap' . EXT;

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::factory()
    ->execute()
    ->send_headers()
    ->body();
