<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Contains the most low-level helpers methods in Kohana:
 *
 * - Environment initialization
 * - Locating files within the cascading filesystem
 * - Auto-loading and transparent extension of classes
 * - Variable and path debugging
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Core {

	// Release version and codename
	const VERSION  = '3.1.5';
	const CODENAME = 'biarmicus';

	// Common environment type constants for consistency and convenience
	const PRODUCTION  = 1;
	const STAGING     = 2;
	const TESTING     = 3;
	const DEVELOPMENT = 4;

	// Security check that is added to all generated PHP files
	const FILE_SECURITY = '<?php defined(\'SYSPATH\') OR die(\'No direct script access.\');';

	// Format of cache files: header, cache name, and data
	const FILE_CACHE = ":header \n\n// :name\n\n:data\n";

	/**
	 * @var  string  Current environment name
	 */
	public static $environment = Kohana::DEVELOPMENT;

	/**
	 * @var  boolean  True if Kohana is running from the command line
	 */
	public static $is_cli = FALSE;

	/**
	 * @var  boolean  True if Kohana is running on windows
	 */
	public static $is_windows = FALSE;

	/**
	 * @var  boolean  True if [magic quotes](http://php.net/manual/en/security.magicquotes.php) is enabled.
	 */
	public static $magic_quotes = FALSE;

	/**
	 * @var  boolean  Should errors and exceptions be logged
	 */
	public static $log_errors = FALSE;

	/**
	 * @var  boolean  TRUE if PHP safe mode is on
	 */
	public static $safe_mode = FALSE;

	/**
	 * @var  string
	 */
	public static $content_type = 'text/html';

	/**
	 * @var  string  character set of input and output
	 */
	public static $charset = 'utf-8';

	/**
	 * @var  string  the name of the server Kohana is hosted upon
	 */
	public static $server_name = '';

	/**
	 * @var  array   list of valid host names for this instance
	 */
	public static $hostnames = array();

	/**
	 * @var  string  base URL to the application
	 */
	public static $base_url = '/';

	/**
	 * @var  string  Application index file, added to links generated by Kohana. Set by [Kohana::init]
	 */
	public static $index_file = 'index.php';

	/**
	 * @var  string  Cache directory, used by [Kohana::cache]. Set by [Kohana::init]
	 */
	public static $cache_dir;

	/**
	 * @var  integer  Default lifetime for caching, in seconds, used by [Kohana::cache]. Set by [Kohana::init]
	 */
	public static $cache_life = 60;

	/**
	 * @var  boolean  Whether to use internal caching for [Kohana::find_file], does not apply to [Kohana::cache]. Set by [Kohana::init]
	 */
	public static $caching = FALSE;

	/**
	 * @var  boolean  Whether to enable [profiling](kohana/profiling). Set by [Kohana::init]
	 */
	public static $profiling = TRUE;

	/**
	 * @var  boolean  Enable Kohana catching and displaying PHP errors and exceptions. Set by [Kohana::init]
	 */
	public static $errors = TRUE;

	/**
	 * @var  array  Types of errors to display at shutdown
	 */
	public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);

	/**
	 * @var  boolean  set the X-Powered-By header
	 */
	public static $expose = FALSE;

	/**
	 * @var  Log  logging object
	 */
	public static $log;

	/**
	 * @var  Config  config object
	 */
	public static $config;

	/**
	 * @var  boolean  Has [Kohana::init] been called?
	 */
	protected static $_init = FALSE;

	/**
	 * @var  array   Currently active modules
	 */
	protected static $_modules = array();

	/**
	 * @var  array   Include paths that are used to find files
	 */
	protected static $_paths = array(APPPATH, SYSPATH);

	/**
	 * @var  array   File path cache, used when caching is true in [Kohana::init]
	 */
	protected static $_files = array();

	/**
	 * @var  boolean  Has the file path cache changed during this execution?  Used internally when when caching is true in [Kohana::init]
	 */
	protected static $_files_changed = FALSE;

	/**
	 * Initializes the environment:
	 *
	 * - Disables register_globals and magic_quotes_gpc
	 * - Determines the current environment
	 * - Set global settings
	 * - Sanitizes GET, POST, and COOKIE variables
	 * - Converts GET, POST, and COOKIE variables to the global character set
	 *
	 * The following settings can be set:
	 *
	 * Type      | Setting    | Description                                    | Default Value
	 * ----------|------------|------------------------------------------------|---------------
	 * `string`  | base_url   | The base URL for your application.  This should be the *relative* path from your DOCROOT to your `index.php` file, in other words, if Kohana is in a subfolder, set this to the subfolder name, otherwise leave it as the default.  **The leading slash is required**, trailing slash is optional.   | `"/"`
	 * `string`  | index_file | The name of the [front controller](http://en.wikipedia.org/wiki/Front_Controller_pattern).  This is used by Kohana to generate relative urls like [HTML::anchor()] and [URL::base()]. This is usually `index.php`.  To [remove index.php from your urls](tutorials/clean-urls), set this to `FALSE`. | `"index.php"`
	 * `string`  | charset    | Character set used for all input and output    | `"utf-8"`
	 * `string`  | cache_dir  | Kohana's cache directory.  Used by [Kohana::cache] for simple internal caching, like [Fragments](kohana/fragments) and **\[caching database queries](this should link somewhere)**.  This has nothing to do with the [Cache module](cache). | `APPPATH."cache"`
	 * `integer` | cache_life | Lifetime, in seconds, of items cached by [Kohana::cache]         | `60`
	 * `boolean` | errors     | Should Kohana catch PHP errors and uncaught Exceptions and show the `error_view`. See [Error Handling](kohana/errors) for more info. <br /> <br /> Recommended setting: `TRUE` while developing, `FALSE` on production servers. | `TRUE`
	 * `boolean` | profile    | Whether to enable the [Profiler](kohana/profiling). <br /> <br />Recommended setting: `TRUE` while developing, `FALSE` on production servers. | `TRUE`	 * `boolean` | caching    | Cache file locations to speed up [Kohana::find_file].  This has nothing to do with [Kohana::cache], [Fragments](kohana/fragments) or the [Cache module](cache).  <br /> <br />  Recommended setting: `FALSE` while developing, `TRUE` on production servers. | `FALSE`
	 *
	 * @throws  Kohana_Exception
	 * @param   array   $settings   Array of settings.  See above.
	 * @return  void
	 * @uses    Kohana::globals
	 * @uses    Kohana::sanitize
	 * @uses    Kohana::cache
	 * @uses    Profiler
	 */
	public static function init(array $settings = NULL)
	{
		if (Kohana::$_init)
		{
			// Do not allow execution twice
			return;
		}

		// Kohana is now initialized
		Kohana::$_init = TRUE;

		if (isset($settings['profile']))
		{
			// Enable profiling
			Kohana::$profiling = (bool) $settings['profile'];
		}

		// Start an output buffer
		ob_start();

		if (isset($settings['errors']))
		{
			// Enable error handling
			Kohana::$errors = (bool) $settings['errors'];
		}

		if (Kohana::$errors === TRUE)
		{
			// Enable Kohana exception handling, adds stack traces and error source.
			set_exception_handler(array('Kohana_Exception', 'handler'));

			// Enable Kohana error handling, converts all PHP errors to exceptions.
			set_error_handler(array('Kohana', 'error_handler'));
		}

		// Enable the Kohana shutdown handler, which catches E_FATAL errors.
		register_shutdown_function(array('Kohana', 'shutdown_handler'));

		if (ini_get('register_globals'))
		{
			// Reverse the effects of register_globals
			Kohana::globals();
		}

		if (isset($settings['expose']))
		{
			Kohana::$expose = (bool) $settings['expose'];
		}

		// Determine if we are running in a command line environment
		Kohana::$is_cli = (PHP_SAPI === 'cli');

		// Determine if we are running in a Windows environment
		Kohana::$is_windows = (DIRECTORY_SEPARATOR === '\\');

		// Determine if we are running in safe mode
		Kohana::$safe_mode = (bool) ini_get('safe_mode');

		if (isset($settings['cache_dir']))
		{
			if ( ! is_dir($settings['cache_dir']))
			{
				try
				{
					// Create the cache directory
					mkdir($settings['cache_dir'], 0755, TRUE);

					// Set permissions (must be manually set to fix umask issues)
					chmod($settings['cache_dir'], 0755);
				}
				catch (Exception $e)
				{
					throw new Kohana_Exception('Could not create cache directory :dir',
						array(':dir' => Debug::path($settings['cache_dir'])));
				}
			}

			// Set the cache directory path
			Kohana::$cache_dir = realpath($settings['cache_dir']);
		}
		else
		{
			// Use the default cache directory
			Kohana::$cache_dir = APPPATH.'cache';
		}

		if ( ! is_writable(Kohana::$cache_dir))
		{
			throw new Kohana_Exception('Directory :dir must be writable',
				array(':dir' => Debug::path(Kohana::$cache_dir)));
		}

		if (isset($settings['cache_life']))
		{
			// Set the default cache lifetime
			Kohana::$cache_life = (int) $settings['cache_life'];
		}

		if (isset($settings['caching']))
		{
			// Enable or disable internal caching
			Kohana::$caching = (bool) $settings['caching'];
		}

		if (Kohana::$caching === TRUE)
		{
			// Load the file path cache
			Kohana::$_files = Kohana::cache('Kohana::find_file()');
		}

		if (isset($settings['charset']))
		{
			// Set the system character set
			Kohana::$charset = strtolower($settings['charset']);
		}

		if (function_exists('mb_internal_encoding'))
		{
			// Set the MB extension encoding to the same character set
			mb_internal_encoding(Kohana::$charset);
		}

		if (isset($settings['base_url']))
		{
			// Set the base URL
			Kohana::$base_url = rtrim($settings['base_url'], '/').'/';
		}

		if (isset($settings['index_file']))
		{
			// Set the index file
			Kohana::$index_file = trim($settings['index_file'], '/');
		}

		// Determine if the extremely evil magic quotes are enabled
		Kohana::$magic_quotes = (bool) get_magic_quotes_gpc();

		// Sanitize all request variables
		$_GET    = Kohana::sanitize($_GET);
		$_POST   = Kohana::sanitize($_POST);
		$_COOKIE = Kohana::sanitize($_COOKIE);

		// Load the logger
		Kohana::$log = Log::instance();

		// Load the config
		Kohana::$config = Config::instance();
	}

	/**
	 * Cleans up the environment:
	 *
	 * - Restore the previous error and exception handlers
	 * - Destroy the Kohana::$log and Kohana::$config objects
	 *
	 * @return  void
	 */
	public static function deinit()
	{
		if (Kohana::$_init)
		{
			// Removed the autoloader
			spl_autoload_unregister(array('Kohana', 'auto_load'));

			if (Kohana::$errors)
			{
				// Go back to the previous error handler
				restore_error_handler();

				// Go back to the previous exception handler
				restore_exception_handler();
			}

			// Destroy objects created by init
			Kohana::$log = Kohana::$config = NULL;

			// Reset internal storage
			Kohana::$_modules = Kohana::$_files = array();
			Kohana::$_paths   = array(APPPATH, SYSPATH);

			// Reset file cache status
			Kohana::$_files_changed = FALSE;

			// Kohana is no longer initialized
			Kohana::$_init = FALSE;
		}
	}

	/**
	 * Reverts the effects of the `register_globals` PHP setting by unsetting
	 * all global varibles except for the default super globals (GPCS, etc),
	 * which is a [potential security hole.][ref-wikibooks]
	 *
	 * This is called automatically by [Kohana::init] if `register_globals` is
	 * on.
	 *
	 *
	 * [ref-wikibooks]: http://en.wikibooks.org/wiki/PHP_Programming/Register_Globals
	 *
	 * @return  void
	 */
	public static function globals()
	{
		if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS']))
		{
			// Prevent malicious GLOBALS overload attack
			echo "Global variable overload attack detected! Request aborted.\n";

			// Exit with an error status
			exit(1);
		}

		// Get the variable names of all globals
		$global_variables = array_keys($GLOBALS);

		// Remove the standard global variables from the list
		$global_variables = array_diff($global_variables, array(
			'_COOKIE',
			'_ENV',
			'_GET',
			'_FILES',
			'_POST',
			'_REQUEST',
			'_SERVER',
			'_SESSION',
			'GLOBALS',
		));

		foreach ($global_variables as $name)
		{
			// Unset the global variable, effectively disabling register_globals
			unset($GLOBALS[$name]);
		}
	}

	/**
	 * Recursively sanitizes an input variable:
	 *
	 * - Strips slashes if magic quotes are enabled
	 * - Normalizes all newlines to LF
	 *
	 * @param   mixed   $value  any variable
	 * @return  mixed   sanitized variable
	 */
	public static function sanitize($value)
	{
		if (is_array($value) OR is_object($value))
		{
			foreach ($value as $key => $val)
			{
				// Recursively clean each value
				$value[$key] = Kohana::sanitize($val);
			}
		}
		elseif (is_string($value))
		{
			if (Kohana::$magic_quotes === TRUE)
			{
				// Remove slashes added by magic quotes
				$value = stripslashes($value);
			}

			if (strpos($value, "\r") !== FALSE)
			{
				// Standardize newlines
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}

		return $value;
	}

	/**
	 * Provides auto-loading support of classes that follow Kohana's [class
	 * naming conventions](kohana/conventions#class-names-and-file-location).
	 * See [Loading Classes](kohana/autoloading) for more information.
	 *
	 * Class names are converted to file names by making the class name
	 * lowercase and converting underscores to slashes:
	 *
	 *     // Loads classes/my/class/name.php
	 *     Kohana::auto_load('My_Class_Name');
	 *
	 * You should never have to call this function, as simply calling a class
	 * will cause it to be called.
	 *
	 * This function must be enabled as an autoloader in the bootstrap:
	 *
	 *     spl_autoload_register(array('Kohana', 'auto_load'));
	 *
	 * @param   string  $class  class name
	 * @return  boolean
	 */
	public static function auto_load($class)
	{
		try
		{
			// Transform the class name into a path
			$file = str_replace('_', '/', strtolower($class));

			if ($path = Kohana::find_file('classes', $file))
			{
				// Load the class file
				require $path;

				// Class has been found
				return TRUE;
			}

			// Class is not in the filesystem
			return FALSE;
		}
		catch (Exception $e)
		{
			Kohana_Exception::handler($e);
			die;
		}
	}

	/**
	 * Changes the currently enabled modules. Module paths may be relative
	 * or absolute, but must point to a directory:
	 *
	 *     Kohana::modules(array('modules/foo', MODPATH.'bar'));
	 *
	 * @param   array   $modules    list of module paths
	 * @return  array   enabled modules
	 */
	public static function modules(array $modules = NULL)
	{
		if ($modules === NULL)
		{
			// Not changing modules, just return the current set
			return Kohana::$_modules;
		}

		// Start a new list of include paths, APPPATH first
		$paths = array(APPPATH);

		foreach ($modules as $name => $path)
		{
			if (is_dir($path))
			{
				// Add the module to include paths
				$paths[] = $modules[$name] = realpath($path).DIRECTORY_SEPARATOR;
			}
			else
			{
				// This module is invalid, remove it
				unset($modules[$name]);
			}
		}

		// Finish the include paths by adding SYSPATH
		$paths[] = SYSPATH;

		// Set the new include paths
		Kohana::$_paths = $paths;

		// Set the current module list
		Kohana::$_modules = $modules;

		foreach (Kohana::$_modules as $path)
		{
			$init = $path.'init'.EXT;

			if (is_file($init))
			{
				// Include the module initialization file once
				require_once $init;
			}
		}

		return Kohana::$_modules;
	}

	/**
	 * Returns the the currently active include paths, including the
	 * application, system, and each module's path.
	 *
	 * @return  array
	 */
	public static function include_paths()
	{
		return Kohana::$_paths;
	}

	/**
	 * Searches for a file in the [Cascading Filesystem](kohana/files), and
	 * returns the path to the file that has the highest precedence, so that it
	 * can be included.
	 *
	 * When searching the "config", "messages", or "i18n" directories, or when
	 * the `$array` flag is set to true, an array of all the files that match
	 * that path in the [Cascading Filesystem](kohana/files) will be returned.
	 * These files will return arrays which must be merged together.
	 *
	 * If no extension is given, the default extension (`EXT` set in
	 * `index.php`) will be used.
	 *
	 *     // Returns an absolute path to views/template.php
	 *     Kohana::find_file('views', 'template');
	 *
	 *     // Returns an absolute path to media/css/style.css
	 *     Kohana::find_file('media', 'css/style', 'css');
	 *
	 *     // Returns an array of all the "mimes" configuration files
	 *     Kohana::find_file('config', 'mimes');
	 *
	 * @param   string  $dir    directory name (views, i18n, classes, extensions, etc.)
	 * @param   string  $file   filename with subdirectory
	 * @param   string  $ext    extension to search for
	 * @param   boolean $array  return an array of files?
	 * @return  array   a list of files when $array is TRUE
	 * @return  string  single file path
	 */
	public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
	{
		if ($ext === NULL)
		{
			// Use the default extension
			$ext = EXT;
		}
		elseif ($ext)
		{
			// Prefix the extension with a period
			$ext = ".{$ext}";
		}
		else
		{
			// Use no extension
			$ext = '';
		}

		// Create a partial path of the filename
		$path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

		if (Kohana::$caching === TRUE AND isset(Kohana::$_files[$path.($array ? '_array' : '_path')]))
		{
			// This path has been cached
			return Kohana::$_files[$path.($array ? '_array' : '_path')];
		}

		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Kohana', __FUNCTION__);
		}

		if ($array OR $dir === 'config' OR $dir === 'i18n' OR $dir === 'messages')
		{
			// Include paths must be searched in reverse
			$paths = array_reverse(Kohana::$_paths);

			// Array of files that have been found
			$found = array();

			foreach ($paths as $dir)
			{
				if (is_file($dir.$path))
				{
					// This path has a file, add it to the list
					$found[] = $dir.$path;
				}
			}
		}
		else
		{
			// The file has not been found yet
			$found = FALSE;

			foreach (Kohana::$_paths as $dir)
			{
				if (is_file($dir.$path))
				{
					// A path has been found
					$found = $dir.$path;

					// Stop searching
					break;
				}
			}
		}

		if (Kohana::$caching === TRUE)
		{
			// Add the path to the cache
			Kohana::$_files[$path.($array ? '_array' : '_path')] = $found;

			// Files have been changed
			Kohana::$_files_changed = TRUE;
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $found;
	}

	/**
	 * Recursively finds all of the files in the specified directory at any
	 * location in the [Cascading Filesystem](kohana/files), and returns an
	 * array of all the files found, sorted alphabetically.
	 *
	 *     // Find all view files.
	 *     $views = Kohana::list_files('views');
	 *
	 * @param   string  $directory  directory name
	 * @param   array   $paths      list of paths to search
	 * @return  array
	 */
	public static function list_files($directory = NULL, array $paths = NULL)
	{
		if ($directory !== NULL)
		{
			// Add the directory separator
			$directory .= DIRECTORY_SEPARATOR;
		}

		if ($paths === NULL)
		{
			// Use the default paths
			$paths = Kohana::$_paths;
		}

		// Create an array for the files
		$found = array();

		foreach ($paths as $path)
		{
			if (is_dir($path.$directory))
			{
				// Create a new directory iterator
				$dir = new DirectoryIterator($path.$directory);

				foreach ($dir as $file)
				{
					// Get the file name
					$filename = $file->getFilename();

					if ($filename[0] === '.' OR $filename[strlen($filename)-1] === '~')
					{
						// Skip all hidden files and UNIX backup files
						continue;
					}

					// Relative filename is the array key
					$key = $directory.$filename;

					if ($file->isDir())
					{
						if ($sub_dir = Kohana::list_files($key, $paths))
						{
							if (isset($found[$key]))
							{
								// Append the sub-directory list
								$found[$key] += $sub_dir;
							}
							else
							{
								// Create a new sub-directory list
								$found[$key] = $sub_dir;
							}
						}
					}
					else
					{
						if ( ! isset($found[$key]))
						{
							// Add new files to the list
							$found[$key] = realpath($file->getPathName());
						}
					}
				}
			}
		}

		// Sort the results alphabetically
		ksort($found);

		return $found;
	}

	/**
	 * Loads a file within a totally empty scope and returns the output:
	 *
	 *     $foo = Kohana::load('foo.php');
	 *
	 * @param   string  $file
	 * @return  mixed
	 */
	public static function load($file)
	{
		return include $file;
	}

	/**
	 * Returns the configuration array for the requested group.  See
	 * [configuration files](kohana/files/config) for more information.
	 *
	 *     // Get all the configuration in config/database.php
	 *     $config = Kohana::config('database');
	 *
	 *     // Get only the default connection configuration
	 *     $default = Kohana::config('database.default')
	 *
	 *     // Get only the hostname of the default connection
	 *     $host = Kohana::config('database.default.connection.hostname')
	 *
	 * @param   string  $group  group name
	 * @return  Config
	 */
	public static function config($group)
	{
		static $config;

		if (strpos($group, '.') !== FALSE)
		{
			// Split the config group and path
			list ($group, $path) = explode('.', $group, 2);
		}

		if ( ! isset($config[$group]))
		{
			// Load the config group into the cache
			$config[$group] = Kohana::$config->load($group);
		}

		if (isset($path))
		{
			return Arr::path($config[$group], $path, NULL, '.');
		}
		else
		{
			return $config[$group];
		}
	}

	/**
	 * Provides simple file-based caching for strings and arrays:
	 *
	 *     // Set the "foo" cache
	 *     Kohana::cache('foo', 'hello, world');
	 *
	 *     // Get the "foo" cache
	 *     $foo = Kohana::cache('foo');
	 *
	 * All caches are stored as PHP code, generated with [var_export][ref-var].
	 * Caching objects may not work as expected. Storing references or an
	 * object or array that has recursion will cause an E_FATAL.
	 *
	 * The cache directory and default cache lifetime is set by [Kohana::init]
	 *
	 * [ref-var]: http://php.net/var_export
	 *
	 * @throws  Kohana_Exception
	 * @param   string  $name       name of the cache
	 * @param   mixed   $data       data to cache
	 * @param   integer $lifetime   number of seconds the cache is valid for
	 * @return  mixed    for getting
	 * @return  boolean  for setting
	 */
	public static function cache($name, $data = NULL, $lifetime = NULL)
	{
		// Cache file is a hash of the name
		$file = sha1($name).'.txt';

		// Cache directories are split by keys to prevent filesystem overload
		$dir = Kohana::$cache_dir.DIRECTORY_SEPARATOR.$file[0].$file[1].DIRECTORY_SEPARATOR;

		if ($lifetime === NULL)
		{
			// Use the default lifetime
			$lifetime = Kohana::$cache_life;
		}

		if ($data === NULL)
		{
			if (is_file($dir.$file))
			{
				if ((time() - filemtime($dir.$file)) < $lifetime)
				{
					// Return the cache
					try
					{
						return unserialize(file_get_contents($dir.$file));
					}
					catch (Exception $e)
					{
						// Cache is corrupt, let return happen normally.
					}
				}
				else
				{
					try
					{
						// Cache has expired
						unlink($dir.$file);
					}
					catch (Exception $e)
					{
						// Cache has mostly likely already been deleted,
						// let return happen normally.
					}
				}
			}

			// Cache not found
			return NULL;
		}

		if ( ! is_dir($dir))
		{
			// Create the cache directory
			mkdir($dir, 0777, TRUE);

			// Set permissions (must be manually set to fix umask issues)
			chmod($dir, 0777);
		}

		// Force the data to be a string
		$data = serialize($data);

		try
		{
			// Write the cache
			return (bool) file_put_contents($dir.$file, $data, LOCK_EX);
		}
		catch (Exception $e)
		{
			// Failed to write cache
			return FALSE;
		}
	}

	/**
	 * Get a message from a file. Messages are arbitary strings that are stored
	 * in the `messages/` directory and reference by a key. Translation is not
	 * performed on the returned values.  See [message files](kohana/files/messages)
	 * for more information.
	 *
	 *     // Get "username" from messages/text.php
	 *     $username = Kohana::message('text', 'username');
	 *
	 * @param   string  $file       file name
	 * @param   string  $path       key path to get
	 * @param   mixed   $default    default value if the path does not exist
	 * @return  string  message string for the given path
	 * @return  array   complete message list, when no path is specified
	 * @uses    Arr::merge
	 * @uses    Arr::path
	 */
	public static function message($file, $path = NULL, $default = NULL)
	{
		static $messages;

		if ( ! isset($messages[$file]))
		{
			// Create a new message list
			$messages[$file] = array();

			if ($files = Kohana::find_file('messages', $file))
			{
				foreach ($files as $f)
				{
					// Combine all the messages recursively
					$messages[$file] = Arr::merge($messages[$file], Kohana::load($f));
				}
			}
		}

		if ($path === NULL)
		{
			// Return all of the messages
			return $messages[$file];
		}
		else
		{
			// Get a message using the path
			return Arr::path($messages[$file], $path, $default);
		}
	}

	/**
	 * PHP error handler, converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 *
	 * @throws  ErrorException
	 * @return  TRUE
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			throw new ErrorException($error, $code, 0, $file, $line);
		}

		// Do not execute the PHP error handler
		return TRUE;
	}

	/**
	 * Catches errors that are not caught by the error handler, such as E_PARSE.
	 *
	 * @uses    Kohana_Exception::handler
	 * @return  void
	 */
	public static function shutdown_handler()
	{
		if ( ! Kohana::$_init)
		{
			// Do not execute when not active
			return;
		}

		try
		{
			if (Kohana::$caching === TRUE AND Kohana::$_files_changed === TRUE)
			{
				// Write the file path cache
				Kohana::cache('Kohana::find_file()', Kohana::$_files);
			}
		}
		catch (Exception $e)
		{
			// Pass the exception to the handler
			Kohana_Exception::handler($e);
		}

		if (Kohana::$errors AND $error = error_get_last() AND in_array($error['type'], Kohana::$shutdown_errors))
		{
			// Clean the output buffer
			ob_get_level() and ob_clean();

			// Fake an exception for nice debugging
			Kohana_Exception::handler(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

			// Shutdown now to avoid a "death loop"
			exit(1);
		}
	}

} // End Kohana