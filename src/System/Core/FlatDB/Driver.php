<?php

/**
 * FlatDB driver abstract class.
 *
 * @package     Mamuph
 * @category    FlatDB
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */
abstract class Core_FlatDB_Driver
{

    /**
     * @var array   In-memory storage
     */
    public $data = array();
    

    /**
     * Storage the data that was wroted in memory
     * 
     * @return bool
     */
    abstract public function flush();

}