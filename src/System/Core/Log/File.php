<?php


/**
 * File log writer. Writes out messages and stores them in a YYYY/MM directory.
 *
 * @package     Mamuph
 * @category    Log
 * @author      Mamuph Team
 * @copyright   (c) 2015-2016 Mamuph Team
 */
class Core_Log_File extends Log_Writer
{

    /**
     * @var  string  Directory to place log files in
     */
    protected $_directory;


    /**
     * Creates a new file logger. Checks that the directory exists and
     * is writable.
     *
     *     $writer = new Log_File($directory);
     *
     * @param   string  $directory  log directory
     */
    public function __construct($directory)
    {
        if ( ! is_dir($directory) OR ! is_writable($directory))
            throw new Exception('Directory '.$directory.' must be writable');

        // Determine the directory path
        $this->_directory = realpath($directory).DIRECTORY_SEPARATOR;
    }

    /**
     * Writes each of the messages into the log file. The log file will be
     * appended to the `YYYY/MM/DD.log.php` file, where YYYY is the current
     * year, MM is the current month, and DD is the current day.
     *
     *     $writer->write($messages);
     *
     * @param   array   $messages
     * @return  void
     */
    public function write(array $messages)
    {
        // Set the yearly directory name
        $directory = $this->_directory.date('Y');

        if ( ! is_dir($directory))
        {
            // Create the yearly directory
            mkdir($directory);
        }

        // Add the month to the directory
        $directory .= DIRECTORY_SEPARATOR.date('m');

        if ( ! is_dir($directory))
        {
            // Create the monthly directory
            mkdir($directory);
        }

        // Set the name of the log file
        $filename = $directory.DIRECTORY_SEPARATOR.date('d').'.log';

        if ( ! file_exists($filename))
        {
            // Create the log file
            touch($filename);
        }

        foreach ($messages as $message)
        {
            // Write each message into the log file
            file_put_contents($filename, PHP_EOL.$this->format_message($message), FILE_APPEND);
        }
    }

}