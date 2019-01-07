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
define('APPPATH', realpath(DOCROOT . '../guides/3.0/application') . DIRECTORY_SEPARATOR);
define('MODPATH', realpath(DOCROOT . '../guides/3.0/modules') . DIRECTORY_SEPARATOR);
define('SYSPATH', realpath(DOCROOT . '../guides/3.0/system') . DIRECTORY_SEPARATOR);

// Load the base, low-level functions
require SYSPATH . 'base' . EXT;

// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/kohana' . EXT;
}

// Bootstrap the application
require '../guides/3.0-bootstrap' . EXT;
