<?php

/**
 * Parameters helper class
 *
 * @package     Mamuph
 * @category    Helpers
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 *
 */
class Core_Params
{

    /**
     * @var array   Argument list and their values after to be processed
     */
    protected static $definitions;

    /**
     * @var array   List of collected unknown arguments
     */
    protected static $unknown_arguments = [];

    /**
     * @var string  Current executable name
     */
    public static $executable;


    /**
     * Process the argument list according the arguments definition
     *
     * @param array $definitions
     */
    public static function process(array $definitions)
    {

        Params::$definitions = $definitions;

        // Get raw arguments
        $args = $GLOBALS['argv'];

        // Extract executable name
        Params::$executable = $args[0];
        array_shift($args);

        $free_def_sort = 0;

        foreach ($args as $ka => $arg)
        {

            $found = null;

            if (strpos($arg, '-') === 0)
                $found = Params::search_definition($arg);
            else
            {
                $found = Params::search_free_definition($arg, $free_def_sort);
                $free_def_sort++;
            }

            if ($found)
                Params::$definitions = Arr::merge(Params::$definitions, $found);
            else
                Params::$unknown_arguments[] = $arg;

        }

    }



    /**
     * Get parameter value
     *
     * @param $param
     * @return array|bool
     */
    public static function get($param = null)
    {
        
        if (empty($param))
            return Params::$definitions;

        if (!isset(Params::$definitions[$param]) || !isset(Params::$definitions[$param]['value']))
            return false;

        return Params::$definitions[$param]['value'];
    }


    /**
     * Set a parameter value outside the command line
     * @param $param
     * @param $value
     * @return mixed
     */
    public static function set($param, $value)
    {
        return Params::$definitions[$param]['value'] = $value;
    }


    /**
     * Perform a validation
     *
     * @return array
     */
    public static function validate()
    {
        $errors = [];

        foreach (Params::$definitions as $k => $definition)
        {
           if (isset($definition['optional']) && $definition['optional'] === false && !isset($definition['value']))
               $errors[$k] = 'Not passed';
        }

        return empty($errors) ? false : $errors;

    }


    /**
     * Search the definition that match with short_arg or long_arg
     *
     * @param string    $arg    Argument name with hyphens
     * @return array|bool
     */
    protected static function search_definition($arg)
    {

        if (strpos($arg, '--') === 0)
        {
            $type = 'long_arg';
            $arg = substr($arg, 2);
        }
        else if (strpos($arg, '-') === 0)
        {
            $type = 'short_arg';
            $arg = substr($arg, 1);
        }

        // Split value
        $value = null;

        if (strpos($arg, '=') !== false)
        {
            list($arg, $value) = explode('=', $arg, 2);
        }

        // Search argument in definition list
        $found = array_filter(Params::$definitions, function($passed_definition) use ($arg, $type)
        {
            return !empty($passed_definition[$type]) && $passed_definition[$type] === $arg;
        });

        if (empty($found))
            return false;

        $found[key($found)]['value'] = empty($value) ? true : $value;

        return $found;
    }


    /**
     * Search free definitions
     *
     * @param $arg
     * @param int $free_def_sort
     * @return array|bool
     */
    protected static function search_free_definition($arg, $free_def_sort = 0)
    {

        $current_def = -1;

        // Search argument in definition list
        $found = array_filter(Params::$definitions, function($passed_definition) use ($free_def_sort, &$current_def)
        {

            if (isset($passed_definition->short_arg) || isset($passed_definition->long_arg))
                return false;

            $current_def++;

            if ($free_def_sort != $current_def)
                return false;

            return true;
        });


        if (empty($found))
            return false;

        $found[key($found)]['value'] = $arg;

        return $found;
    }

}