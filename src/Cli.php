<?php

/**
 * Command line parser and execute automation code
 * 
 * This class take module name as an argument and try to execute it if module class found.
 * Each module class need extends AbstractCliModule() and implement execute() method
 * 
 * Each module might have different options so typing 
 * php cli.php module1 -h might give you different output
 * than php cli.php module2 -h
 *
 * Typing -h without module name will give you this message.
 * 
 * Module name is case sensitive so myModule is not the same as MyModule
 * 
 * See in to ../examples folder so you will find some interesting solutions
 * 
 * @author Wojciech Brozyna <wojciech.brozyna@gmail.com>
 */

namespace mcommander;

class Cli {
    
    /**
     * Disable it for testing
     */
    public $testing = false;
    
    /**
     * Arguments copy
     * @var array
     */
    private $argsTmp = [];
    
    /**
     * ARGC
     * 
     * @var int
     */
    private $argc;
    
    /**
     * ARGV
     * 
     * @var array
     */
    private $argv = [];
    
    /**
     * Render exception
     * 
     * @param Exception|RuntimeException
     */
    static public function renderException($ex)
    {
        
        CliColors::render("Runtime Error!", CliColors::FG_WHITE, CliColors::BG_RED, true);
        CliColors::render("Exception code: {$ex->getCode()}", CliColors::FG_WHITE, CliColors::BG_RED, true);
        CliColors::render("Exception message: {$ex->getMessage()}", CliColors::FG_WHITE, CliColors::BG_RED, true);
        
    }
    
    /**
     * 
     * Despatch request to approperiate module class. 
     * 
     * @param int $argc Arguments count
     * @param array $argv Arguments list
     */
    public function despatch($argc, $argv)
    {
        
        $this->argsTmp = $argv;
        $this->argc = $argc;
        $this->argv = $argv;
        
        if($this->isHelpNeeded() === true) {
            
            $this->displayHelp();
            
        } else {
            
            $this->executeCommand();
            
        }
        
    }
    
    /**
     * Display help message
     * 
     * @return void
     */
    public function displayHelp()
    {
        
        if($this->testing === true) {
            
            return null;
            
        }
        
        print("\nUsage: ./path_to_commander ModuleName [options] \n");
        print("Example: ./vendor/bin/m-commander MyModule -h \n\n");
        
        $this->yellowOutput("NOTICE! \n");
        print("Each module might have different options \n");
        print("So typing: \n");
        $this->yellowOutput("./path_to_commander MyModule -h \n");
        print("might give you different output than \n");
        $this->yellowOutput("./path_to_commander MyModule2 -h \n");
        print("Typing -h without module name will display this message. \n");
        
    }
    
    /**
     * Check if help message need to be displays
     * 
     * @return bool
     */
    private function isHelpNeeded()
    {
        
        if(count($this->argsTmp) > 2) {
            
            // if there is more arguments
            // it seems like user trying to display help for module, not for this page
            return false;
            
        }
        
        if($this->argsTmp[1] === '-h' || $this->argsTmp[1] === '--help') {
                
            return true;
                
        }
        
        return false;
        
    }
    
    /**
     * Execute command from Cli
     * 
     * @return void
     */
    private function executeCommand()
    {
                
        $this->isModuleProvided();
       
        $this->executeModule();
        
    }
    
    /**
     * Check if module name has been provided
     * 
     * @throws \RuntimeException
     */
    private function isModuleProvided()
    {
        
        if(isset($this->argsTmp[1]) === false ) {
            
            throw new \RuntimeException('Module name not provided', CliCodes::MOD_ERR);
            
        }
        
        return true;
        
    }
    
    /**
     * Execute module 
     * 
     * @throw RuntimeException
     */
    private function executeModule()
    {
        
        // after filtering will be always as a last array element
        $module = $this->argsTmp[1];
        
        if(class_exists($module) === true) {

            // pass original vars in to module constructor
            $obj = new $module($this->argc, $this->argv);
            
            // execute() function is in AbstractCliModule() class
            call_user_func(array($obj, 'execute'));

        } else {

            throw new \RuntimeException("Module '{$module}' not found", CliCodes::MOD_NOT_FOUND);

        }
        
    }
    
    /**
     * Colour output for help message
     * 
     * @param string $string
     * @return void
     */
    private function yellowOutput($string)
    {
        
        CliColors::render($string, CliColors::FG_YELLOW);
        
    }
    
}
