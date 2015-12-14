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
     * Module options (CLI arguments)
     * Where array key is the option and value is callable method.
     * Callable method need to exists in CLI module.
     * 
     * Add new option by calling $this->addOption('name', 'callback');
     * 
     * @var array
     */
    private $defaultOptions = array('-v' => 'verbose', 
                                    '--verbose' => 'verbose', 
                                    '-h' => 'helpMsg', 
                                    '--help' => 'helpMsg');
    
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
        
        $this->setupInternalArgs();
               
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
     * Add option 
     * Run this function in constructor only, before parent::__construct()
     * otherwise will not work.
     * 
     * @param string $name Option name
     * @param string $callback Callable function in this object
     */
    protected function addOption($name, $callback)
    {
        
        $this->defaultOptions[$name] = $callback;
        
    }
    
    /**
     * Show help message
     * 
     * @return void
     */
    protected function helpMsg()
    {
        
        print("This module doesn't have help page \n");
        
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
     * Setup internal arguments options
     * Each module might have different options.
     * 
     * @return void
     */
    private function setupInternalArgs()
    {
       
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
    private function loadInternalOption($value)
    {
        
        if(isset($this->defaultOptions[$value])) {
                    
            if(method_exists($this, $this->defaultOptions[$value])) {
                
                //execute callable method
                $this->{$this->defaultOptions[$value]}();
                
            } else {
                
                throw new \RuntimeException("Option method doesn't exists", CliCodes::OPT_METH_ERR);
                
            }
            

        } else {

            throw new \RuntimeException("Invalid argument: {$value}", CliCodes::OPT_FAIL);

        }
        
    }
    
}
