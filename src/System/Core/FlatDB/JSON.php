<?php

/**
 * Serialization driver for FlatDB (JSON format).
 *
 * @package     Mamuph
 * @category    FlatDB
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */
class Core_FlatDB_JSON extends FlatDB_Driver
{

    /**
     * @var string  Path to datafile
     */
    protected $_datafile_path;


    /**
     * Core_FlatDB_Serialize constructor.
     *
     * @param $datafile_path
     */
    public function __construct($datafile_path)
    {
        $this->_datafile_path = $datafile_path;

        // Open file if exists
        if (File::exists($this->_datafile_path))
        {
            $this->data = file_get_contents(json_decode($this->_datafile_path));

            if ($this->data === false)
                throw new ErrorException('Unable to open ' . $this->_datafile_path);

            if (json_last_error() != JSON_ERROR_NONE)
                throw new ErrorException('Wrong JSON format at ' . $this->_datafile_path);
        }

    }


    /**
     * Writes the memory content into a JSON file
     *
     * @return int|boolean
     */
    public function flush()
    {
        return file_put_contents($this->_datafile_path, json_encode($this->data), LOCK_EX);
    }

}
