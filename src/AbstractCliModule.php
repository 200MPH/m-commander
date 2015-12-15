<?php

/**
 * Abstract Cli Module
 * Extend this class to create your own module
 * See in to ../examples folder so you will find some interesting solutions
 *
 * @author Wojciech Brozyna <wojciech.brozyna@gmail.com>
 */

namespace mcommander;

abstract class AbstractCliModule {
    
    /**
     * Args count
     * 
     * @var int
     */
    protected $argc;
    
    /**
     * Cli arguments
     * 
     * @var array
     */
    protected $args = [];
    
    /**
     * Default options
     * 
     * @var array
     */
    protected $defaultOptions = [];
    
    /**
     * Verbose mode
     * If TRUE will output results to the console
     * 
     * @var bool
     */
    private $verbose = false;
    
    /**
     * Write output file
     * 
     * @var bool|string
     */
    private $writeOutputFile = false;
    
    /**
     * Execute command for module
     * Do the job you want to do in this function
     * 
     * @return void
     */
    abstract public function execute(); 
    
    /**
     * 
     * @param int $cliArgsCount CLI arguments count
     * @param array $cliArgs CLI arguments
     */
    public function __construct($cliArgsCount, $cliArgs) 
    {
        
        $this->argc = $cliArgsCount;
        $this->args = $cliArgs;
        
        $this->setupOptions();
               
    }
    
    /**
     * Render standard gray text output
     * 
     * @param string Text to display
     * @return void
     */
    protected function output($string)
    {
        
        if($this->verbose === true) {
            
            print($string);
            
        }
        
        $this->saveOutput($string);
        
    }
    
    /**
     * Render success text output
     * Display green text in to console output.
     * For more colors please use CliColors::render()
     * 
     * @param string Text to display
     * @return void
     */
    protected function successOutput($string)
    {
        
        if($this->verbose === true) {
        
            CliColors::render($string, CliColors::FG_GREEN, null);
        
        }
        
        $this->saveOutput($string);
        
    }
    
    /**
     * Render error text output
     * Display red text in to console output.
     * For more colors please use CliColors::render()
     * 
     * @param string Text to display
     * @return void
     */
    protected function errorOutput($string)
    {
        
        if($this->verbose === true) {
            
            CliColors::render($string, CliColors::FG_RED, null);
            
        }
        
        $this->saveOutput($string);
        
    }
    
    /**
     * Render warning text output
     * Display yellow text in to console output.
     * For more colors please use CliColors::render()
     * 
     * @param string Text to display
     * @return void
     */
    protected function warningOutput($string)
    {
        
        if($this->verbose === true) {
            
            CliColors::render($string, CliColors::FG_YELLOW, null);
            
        }
        
        $this->saveOutput($string);
        
    }
    
    /**
     * Show help message for module
     * 
     * @return void
     */
    protected function helpMsg()
    {
        
        foreach($this->defaultOptions as $array) {
            
            foreach($array['options'] as $option) {
                
                CliColors::render($option . PHP_EOL, CliColors::FG_GREEN);
                
            }
            
            print("\t\t\t");
            
            CliColors::render($array['description'] . PHP_EOL, CliColors::FG_YELLOW);
            
        }
        
        exit();
        
    }
    
    /**
     * Set verbose
     * 
     * @return void
     */
    protected function verbose()
    {
        
        print("Verbose mode ");
        
        CliColors::render("ON", CliColors::FG_GREEN, null, true);
        
        $this->verbose = true;
        
    }
    
    /**
     * Write output into file
     * 
     * @return void
     */
    protected function writeOutput()
    {
        
        foreach($this->args as $key => $value) {
            
            if($value === '-w' || $value === '--write-output') {
                
                $this->isPathNameProvided($key);
                    
                $this->isFileWritable($key);
                
                break;
                
            }
            
        }
        
    }
    
    /**
     * Load default options
     * 
     * @return void
     */
    protected function loadOptions()
    {
        
        $this->defaultOptions[] = array('options' => array('-h', '--help'), 
                                        'callback' => 'helpMsg', 
                                        'description' => 'Display this page');
        
        $this->defaultOptions[] = array('options' => array('-v', '--verbose'), 
                                        'callback' => 'verbose', 
                                        'description' => 'Verbose mode');
        
        $this->defaultOptions[] = array('options' => array('-w', '--write-output'), 
                                        'callback' => 'writeOutput', 
                                        'description' => 'Write output in to file. Eg "./m-commander myNamespace\\\MyModule -w /home/user/test.log"');
        
    }
    
    
    /**
     * Setup internal arguments options
     * Each module might have different options.
     * 
     * @return void
     */
    final private function setupOptions()
    {
       
        $this->loadOptions();
        
        //first arg is path we don't need it here
        array_shift($this->args);
        
        foreach ($this->args as $value) {
            
            if(strpos($value, '-') !== false || strpos($value, '--') !== false) {
                
                $this->loadInternalOption($value);
                
            }
            
        }
                
    }
    
    /**
     * Execute internal option
     * 
     * @param string $value Option value
     * @throw RuntimeException Invalid argument
     */
    final private function loadInternalOption($value)
    {
        
        foreach($this->defaultOptions as $array) {
            
            if(in_array($value, $array['options']) === false) {
                
                continue;
                
            }
                
            if(method_exists($this, $array['callback'])) {

                ///execute callable method
                $this->{$array['callback']}();

                return 0;

            } else {

                throw new \RuntimeException("Option method doesn't exists", CliCodes::OPT_METH_ERR);

            }
            
        }
        
        throw new \RuntimeException("Invalid argument: {$value} \nTry -h or --help to see all available options", CliCodes::OPT_FAIL);
        
    }
    
    /**
     * Save output in to file
     * 
     * @param string $string
     */
    final private function saveOutput($string)
    {
        
        if($this->writeOutputFile !== false) {
            
            file_put_contents($this->writeOutputFile, $string, FILE_APPEND);
            
        }
        
    }
    
    /**
     * Check if path is provided for -w|--write-output option
     * 
     * @var int $optionLocation Expected -w option location in ARGS array.
     * In another word, ARGS array key where -w|--write-output option occured
     * 
     * @throw RuntimeException
     */
    final private function isPathNameProvided($optionLocation)
    {
        
        // next to argument should be file name
        $pathLocation = $optionLocation + 1;
       
        if(isset( $this->args[$pathLocation] ) === false) {

            throw new \RuntimeException('You have to specify path to the file for -w|--write-output option', CliCodes::OPT_WRITE_NO_FILE);

        }
        
    }
    
    /**
     * Check if file is writable
     * 
     * @var int $optionLocation Expected -w option location in ARGS array.
     * In another word, ARGS array key where -w|--write-output option occured
     * 
     * @throw RuntimeException
     */
    final private function isFileWritable($optionLocation)
    {
        
        // next to argument should be file name
        $pathLocation = $optionLocation + 1;
        
        $this->writeOutputFile = $this->args[$pathLocation];

        if(file_put_contents($this->writeOutputFile, '') === false) {

            // looks like we can't create the file
            throw new \RuntimeException('File is not writable. Check filename and permissions', CliCodes::OPT_FILE_PER_ERR);

        }
        
    }
    
}
