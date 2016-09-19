<?php



/**
 * File-based configuration writer. Multiple configuration directories can be
 * used by attaching multiple instances of this class to [Core_Config].
 *
 * @package    Mamuph
 * @category   Configuration
 * @author     Mamuph Team
 * @copyright  (c) 2009-2016 Mamuph Team
 */
class Core_Config_File_Writer implements Core_Config_Writer {

    protected $file_path_cache = array();


    /**
     * The directory where config files are located
     * @var string
     */
    protected $_directory = '';

    /**
     * Creates a new file reader using the given directory as a config source
     *
     * @param string    $directory  Configuration directory to search
     */
    public function __construct($directory = 'Config')
    {
        // Set the configuration directory name
        $this->_directory = trim($directory);
    }


    /**
     * Write and merge the configuration
     *
     * @param string $group
     * @param string $key
     * @param array $config
     */
    public function write($group, $key, $config)
    {

        if ($files = Apprunner::find_file($this->_directory, $group, NULL, TRUE))
        {
            foreach ($files as $file)
            {
                // Merge each file to the configuration array
                $sconfig = Apprunner::includes($file);
                $sconfig[$key] = $config;

                // Write buffer content
                file_put_contents($file, "<?php\n return " . var_export($sconfig, true) . ';', LOCK_EX);
            }
        }
    }

}
