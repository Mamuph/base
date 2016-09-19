<?php


/**
 * Lock writer abstract class. All [Lock] writers must extend this class.
 *
 * @package     Mamuph
 * @category    Lock
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */
abstract class Core_Lock_Writer
{

    /**
     * Write an array of messages.
     *
     *     $writer->write($messages);
     *
     * @param   mixed   $messages
     * @return  void
     */
    abstract public function write($messages);


    /**
     * Read the lock file content
     *
     *      $writer->read();
     *
     * @return mixed
     */
    abstract public function read();


    /**
     * Remove the lock reference
     *
     *      $writer->destroy();
     *
     * @return bool
     */
    abstract public function destroy();


    /**
     *
     * @return mixed
     */
    abstract public function exists();

    /**
     * Allows the writer to have a unique key when stored.
     *
     *     echo $writer;
     *
     * @return  string
     */
    final public function __toString()
    {
        return spl_object_hash($this);
    }

}