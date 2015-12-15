<?php

/**
 * Abstract Cli Module
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
    private $defaultOptions = [];
    
    /**
     * Verbose mode
     * If TRUE will output results to the console
     * 
     * @var bool
     */
    private $verbose = false;
    
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
     * Load default options
     * 
     * @return void
     */
    protected function loadOptions()
    {
        
        $this->defaultOptions[] = array('options' => array('-h', '--help'), 
                                        'callback' => 'helpMsg', 
                                        'description' => 'Print this ');
        
        $this->defaultOptions[] = array('options' => array('-v', '--verbose'), 
                                        'callback' => 'verbose', 
                                        'description' => 'Verbose mode');
        
        $this->defaultOptions[] = array('options' => array('-w', '--write-output'), 
                                        'callback' => 'writeOutput', 
                                        'description' => 'Write output in to file.');
        
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
    
}
