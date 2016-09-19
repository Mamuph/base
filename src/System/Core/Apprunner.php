<?php


/**
 * Abstract Apprunner class. Apprunner is the Mamuph core class
 *
 * @package     Mamuph
 * @category    Apprunner
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */
abstract class Core_Apprunner
{

    // Default namespace prefix
    const NAMESPACE_PREFIX = 'Mamuph\\';

    // Mamuph core version
    const VERSION = '1.1';

    // Environment constants (Can be used as bitmask)
    const DEVELOPMENT   = 0b0001;
    const TESTING       = 0b0010;
    const STAGING       = 0b0100;
    const PRODUCTION    = 0b1000;

    // Default exit codes
    const EXIT_SUCCESS   = 0;
    const EXIT_FAILURE   = 1;


    /**
     * @var     string  Default environment
     */
    public static $environment = Apprunner::DEVELOPMENT;


    /**
     * @var     string  Character set of input and output
     */
    public static $charset = 'utf-8';


    /**
     * @var  array   Include paths that are used to find files
     */
    protected static $_paths = [APPPATH, SYSPATH, CONFIGPATH];


    protected static $_modules = [];

    /**
     * Initializes the environment:
     *
     * - Disables register_globals and magic_quotes_gpc
     * - Determines the current environment
     * - Set global settings
     *
     * The following settings can be set:
     *
     * Type      | Setting    | Description                                    | Default Value
     * ----------|------------|------------------------------------------------|---------------
     * string    | charset    | Character set used for all input and output    | "utf-8"
     *
     * @throws  Exception
     * @param   array   $settings   Array of settings.  See above.
     * @return  void
     */
    public static function init(array $settings = NULL)
    {

        /**
         * Enable xdebug parameter collection in development mode to improve fatal stack traces.
         */
        if (Apprunner::$environment == Apprunner::DEVELOPMENT AND extension_loaded('xdebug'))
        {
            ini_set('xdebug.collect_params', 3);
        }

        if (isset($settings['charset']))
        {
            // Set the system character set
            Apprunner::$charset = strtolower($settings['charset']);
        }

        if (function_exists('mb_internal_encoding'))
        {
            // Set the MB extension encoding to the same character set
            mb_internal_encoding(Apprunner::$charset);
        }

    }


    /**
     * Autoloader class
     *
     * You should never have to call this function because this is the default autoloader method
     *
     * @param   string  $class  Classname
     */
    public static function auto_load($class)
    {

        // Transform the class name according to PSR-0
        $class      = ltrim($class, '\\');
        $file       = '';
        $namespace  = '';

        if ($last_namespace_position = strrpos($class, '\\'))
        {
            // Move to the next registered autoloader when namespace is not the default one
            if (strpos($class, Apprunner::NAMESPACE_PREFIX) !== 0)
                return;

            $namespace = substr($class, 0, $last_namespace_position);
            $class     = substr($class, $last_namespace_position + 1);
            $file      = str_replace('\\', DS, $namespace) . DS;
        }

        $file .= str_replace('_', DS, $class);

        if ($path = Apprunner::find_file('', $file))
        {
            // Load the class file
            self::requires($path);
        }

        // Class is not in the filesystem, so we move to the next registered autoloader...

    }


    /**
     * Changes the currently enabled modules. Module paths may be relative
     * or absolute, but must point to a directory:
     *
     *      Apprunner::modules(array('foomodule' => array('path' => MODPATH.'foo'));
     *
     * @param array|NULL $modules
     * @return array
     * @throws Exception
     */
    public static function modules(array $modules = NULL)
    {
        if ($modules === NULL)
        {
            // Not changing modules, just return the current set
            return Apprunner::$_modules;
        }

        // Start a new list of include paths, APPPATH first
        $paths = [];

        foreach ($modules as $name => $module)
        {

            if (!isset($module['enable_on']) || ($module['enable_on'] & Apprunner::$environment))
            {

                if (is_dir($module['path']))
                {
                    // Add the module to include paths
                    // @ToDo: Add alternative to realpath when PHAR is executed
                    $paths[] = $modules[$name]['path'] = realpath($module['path']) . DS;
                }
                else
                {
                    // This module is invalid, remove it
                    throw new Exception('Attempted to load an invalid or missing module ' . $name);
                }

            }
        }

        // Set the new include paths
        Apprunner::$_paths = Arr::merge(Apprunner::$_paths, $paths);

        // Set the current module list
        Apprunner::$_modules = $modules;

        foreach (Apprunner::$_modules as $module)
        {
            $init = $module['path'] . 'init.php';

            if (is_file($init))
            {
                // Include the module initialization file once
                require_once $init;
            }
        }

        return Apprunner::$_modules;
    }


    /**
     * Find a file
     *
     * @param   string  $dir
     * @param   string  $file
     * @param   string  $ext
     * @param   bool|FALSE $array
     * @return  bool|string
     */
    public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
    {

        if ($ext === NULL)
        {
            // Use the default extension
            $ext = '.php';
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
        $path = $dir . $file . $ext;

        if (is_file($path))
            return $array ? [$path] : $path;

        if ($array || $dir === 'Config')
        {

            // Include paths must be searched in reverse
            $paths = array_reverse(Apprunner::$_paths);

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

            foreach (Apprunner::$_paths as $dir)
            {

                if (is_file($dir . $path))
                {
                    // A path has been found
                    $found = $dir . $path;

                    // Stop searching
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Load script using the require function
     *
     * @param $file
     * @return mixed
     */
    public static function requires($file)
    {
        return require(self::replace_dir_sep($file));
    }


    /**
     * Load script using the include function
     *
     * @param $file
     * @return mixed
     */
    public static function includes($file)
    {
        return include(self::replace_dir_sep($file));
    }


    /**
     *
     */
    public static function execute()
    {
        $args = func_get_args();
        $args_num = func_num_args();

        $file = $args[0];

        $controller_name = 'Controller_'.$file;

        $controller_method = empty($args[1]) ? 'action_main' : 'action_' . $args[1];

        $controller = new $controller_name;

        if ($args_num > 1)
        {

            // Remove just the first argument (Controller name)
            array_shift($args);

            // Remove the second argument (Controller name and Controller method)
            if ($args_num > 2)
                array_shift($args);

            // Call dynamically to the default_method
            call_user_func_array(array($controller, $controller_method), count($args) === 0 ? null : $args);
        }
        else
            $controller->action_main();
    }


    /**
     * Replace the wrong directory separator by the right one
     *
     * @param $file
     * @return mixed
     */
    public static function replace_dir_sep($file)
    {

        if (IS_PHAR)
            $file = str_replace('\\', '/', $file);
        else
            $file = str_replace(DIRECTORY_SEPARATOR == '/' ? '\\' : '/', DS, $file);

        return $file;

    }


    /**
     * Terminate program and return an exit code.
     *
     * @param int $exit_code
     */
    public static function terminate($exit_code = self::EXIT_SUCCESS)
    {
        // @ToDO: call unload routines
        exit($exit_code);
    }

}
