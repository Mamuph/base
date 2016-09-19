<?php
// It is required for unix signaling
declare(ticks = 1);

/**
 * Hooks controller class based in the observed pattern.
 *
 * @package     Mamuph
 * @category    Hooks
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */

abstract class Core_Hooks
{


    /**
     * @var  Hooks  Singleton instance container
     */
    protected static $_instance = array();


    /**
     * @var array   Hook list
     */
    protected static $hooks = array(
        'UNIX_SIGTERM'          => array(),
        'UNIX_SIGHUP'           => array(),
        'UNIX_SIGUSR1'          => array(),
        'MAMUPH_INITIALIZED'    => array(),
        'MAMUPH_TERMINATED'     => array()
    );


    /**
     * Core_Hooks constructor.
     */
    public function __construct()
    {

        if (function_exists('pcntl_signal'))
        {
            pcntl_signal(SIGTERM, function($signal) { Hooks::notify('UNIX_SIGTERM', $signal); });
            pcntl_signal(SIGHUP,  function($signal) { Hooks::notify('UNIX_SIGHUP' , $signal); });
            pcntl_signal(SIGUSR1, function($signal) { Hooks::notify('UNIX_SIGUSR1', $signal); });
        }

    }


    /**
     * Get the singleton instance of this class
     *
     *     $hooks = Hooks::instance();
     *
     * @param   string  $name   Instance name
     * @return  Hooks
     */
    public static function instance($name = 'default')
    {
        if (empty(Hooks::$_instance[$name]))
        {
            // Create a new instance
            Hooks::$_instance[$name] = new Hooks;
        }

        return Hooks::$_instance[$name];
    }


    /**
     * Attach an observer to the hook
     *
     * @param $hookname
     * @param $method
     */
    public static function attach($hookname, $method)
    {
        Hooks::add($hookname);
        Hooks::$hooks[$hookname][] = $method;
    }


    /**
     * Detach an observer from the hook
     *
     * @param $hookname
     * @param $method
     * @return bool
     */
    public static function detach($hookname, $method)
    {

        if (array_key_exists($hookname, Hooks::$hooks))
        {

            $observer_idx = null;

            foreach (Hooks::$hooks[$hookname] as $k => $hook)
            {
                if (is_array($hook))
                {
                    if (array_values($hook) === $method)
                        $observer_idx = $k;

                    break;
                }
                else if ($hook === $method)
                {
                    $observer_idx = $k;

                    break;
                }
            }

            if ($observer_idx != null)
                unset(Hooks::$hooks[$hookname][$observer_idx]);

            return true;

        }

        return false;

    }


    /**
     * Add a new hook
     *
     * @param   string  $hookname   Hook name
     * @return bool
     */
    public static function add($hookname)
    {

        if (!array_key_exists($hookname, Hooks::$hooks))
        {
            Hooks::$hooks[$hookname] = array();
            return true;
        }

        return false;

    }


    /**
     * Notify or call the hooks
     *
     * @param $hookname
     * @param $parameters
     * @throws Exception
     */
    public static function notify($hookname, $parameters = null)
    {
        if (array_key_exists($hookname, Hooks::$hooks))
        {
            foreach (Hooks::$hooks[$hookname] as $observer)
                call_user_func($observer, $parameters);
        }
    }


}