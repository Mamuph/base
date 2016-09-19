<?php


/**
 * Interface for config writers
 *
 * Specifies the methods that a config writer must implement
 *
 * @package Mamuph
 * @author  Mamuph Team
 * @copyright  (c) 2008-2016 Mamuph Team
 */
interface Core_Config_Writer extends Core_Config_Source
{


    /**
     * Writes the passed config in the buffer
     *
     * Returns chainable instance on success or throws
     * Kohana_Config_Exception on failure
     *
     * @param string      $group  The config group
     * @param string      $key    The config key to write to
     * @param array       $config The configuration to write
     * @return boolean
     */
    public function write($group, $key, $config);

}