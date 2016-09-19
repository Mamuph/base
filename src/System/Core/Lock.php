<?php


/**
 * Abstract model class.
 *
 * @package     Mamuph
 * @category    Lock
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */

abstract class Core_Lock
{

    /**
     * @var  Log  Singleton instance container
     */
    protected static $_instance = array();


    /**
     * @var  Lock_Writer  Writer instance
     */
    protected $_writer;


    /**
     * Default dead message
     *
     * 'false' is used when dead message is not used and lock file is not removed
     * 'null' is used when lock file should be removed
     * otherwise dead message is written
     *
     * @var mixed
     */
    public $dead_message = null;


    /**
     * Get the singleton instance of this class and enable writing at shutdown.
     *
     *     $lock = Lock::instance($locker_id);
     *
     * @param   string  Lock instance ID
     * @return  Lock
     */
    public static function instance($id = 'default')
    {
        if (!isset(Lock::$_instance[$id]) || Lock::$_instance[$id] === null)
        {
            // Create a new instance
            Lock::$_instance[$id] = new Lock();

            // Write lock on shutdown
            register_shutdown_function(array(Lock::$_instance[$id], 'dead_message'));
        }

        return Lock::$_instance[$id];
    }


    /**
     * Attaches a lock writer
     *
     *     $lock->attach($writer);
     *
     * @param   Lock_Writer  $writer    instance
     * @param   integer     $min_level  min level to write IF $levels is not an array
     * @return  Lock
     */
    public function attach(Lock_Writer $writer)
    {
        $this->_writer = $writer;

        return $this;
    }


    /**
     * Detaches a lock writer. The same writer object must be used.
     *
     *     $lock->detach($writer);
     *
     * @return  Lock
     */
    public function detach()
    {
        // Remove the writer
        unset($this->_writer);

        return $this;
    }


    /**
     * Write automatically a dead note
     *
     * @return  void
     */
    public function dead_message()
    {
        if ($this->dead_message === null)
            $this->destroy();
        else
            $this->write($this->dead_message);
    }


    /**
     * Write in lockfile
     *
     *     $lock->write($data);
     *
     * @param   mixed   $data   Data or string
     * @return  void
     */
    public function write($data)
    {
        // Write the filtered messages
        $this->_writer->write($data);
    }


    /**
     * Read a lockfile
     *
     *      $lock->read();
     *
     * @return  mixed
     */
    public function read()
    {
        return $this->_writer->read();
    }


    /**
     * Check that Lock exists
     */
    public function exists()
    {
        return $this->_writer->exists();
    }


    /**
     * Delete the lockfile
     */
    public function destroy()
    {
        $this->dead_message = null;

        return $this->_writer->destroy();
    }

}