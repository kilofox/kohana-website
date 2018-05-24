<?php

/**
 * Detect and "route" to guide applications...
 */
if (preg_match('/^\/([\d\.]+)\/guide/', $_SERVER['REQUEST_URI'], $matches)) {
    $version = $matches[1];

    if (is_file('../guides/' . $version . '-index.php')) {
        return include '../guides/' . $version . '-index.php';
    }
}

unset($matches);

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the config/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#application
 */
$application = '../application';

/**
 * The directory in which your modules are located.
 *
 * @see  http://docs.kohanaphp.com/install#modules
 */
$modules = '../modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#system
 */
$system = '../system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://docs.kohanaphp.com/install#ext
 */
define('EXT', '.php');

/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @see  http://php.net/error_reporting
 *
 * When developing your application, it is highly recommended to enable notices
 * and strict warnings. Enable them by using: E_ALL | E_STRICT
 *
 * In a production environment, it is safe to ignore notices and strict warnings.
 * Disable them by using: E_ALL ^ E_NOTICE
 */
error_reporting(E_ALL | E_STRICT);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://docs.kohanaphp.com/bootstrap
 */
// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// Make the application relative to the docroot
if (!is_dir($application) AND is_dir(DOCROOT . $application))
    $application = DOCROOT . $application;

// Make the modules relative to the docroot
if (!is_dir($modules) AND is_dir(DOCROOT . $modules))
    $modules = DOCROOT . $modules;

// Make the system relative to the docroot
if (!is_dir($system) AND is_dir(DOCROOT . $system))
    $system = DOCROOT . $system;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application) . DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules) . DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system) . DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system);

// Define the start time of the application
define('KOHANA_START_TIME', microtime(TRUE));

// Bootstrap the application
require APPPATH . 'bootstrap' . EXT;



/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
$cache = (extension_loaded('apc')) ? HTTP_Cache::factory('apc') : NULL;

echo Request::factory(TRUE, $cache)
    ->execute()
    ->send_headers()
    ->body();
