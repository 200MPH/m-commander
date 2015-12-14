<?php

/**
 * Command line parser and execute automation code
 * 
 * This class take module name as an argument and try to execute it if module class found.
 * Each module class need to be located in CLIModule/Controller
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
 * @author Wojciech Brozyna <wojciech.brozyna@gmail.com>
 */

namespace mcommander;

class Cli {
    
    /**
     * Arguments copy
     * @var array
     */
    private $argsTmp;
    
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
        
        print("\nUsage: ./path_to_commander ModuleName [options] \n");
        print("Example: ./vendor/bin/m-commander MyModule -h \n\n");
        
        $this->yellowOutput("NOTICE! \n");
        print("Each module might have different options so typing: \n");
        $this->yellowOutput("./path_to_commander MyModule -h \nmight give you different output than \n");
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
        
        foreach($this->argsTmp as $key => $value) {
            
            if(strpos($value, '-h') !== false || strpos($value, '--help') !== false) {
                
                return true;
                
            }
            
        }
        
        return false;
        
    }
    
    /**
     * Filter internal options
     */
    private function filter()
    {
        
        foreach($this->argsTmp as $key => $value) {
            
            // skip all internal options like -v or --help
            // because we want to figure out later, that module name has been passed through
            if(strpos($value, '-') !== false) {
                
                unset($this->argsTmp[$key]);
                
            }
            
            if(strpos($value, '--') !== false) {
                
                unset($this->argsTmp[$key]);
                
            }
            
        }
        
    }
    
    /**
     * Check if module has been provided
     * 
     * @throws \RuntimeException
     */
    private function isModuleProvided()
    {
        
        // we removed internal option so ...
        // check if module name is provided as argument
        if(count($this->argsTmp) < 1 ) {
            
            throw new \RuntimeException('Module name not provided', CliCodes::MOD_ERR);
            
        }
        
    }
 
    /**
     * Execute command from Cli
     * 
     * @return void
     */
    private function executeCommand()
    {
        
        $this->filter();
        
        $this->isModuleProvided();
        
        // after filtering will be always as a last array element
        $module = end($this->argsTmp);
        
        // try to execute module if provided
        if($module !== false) {
            
            $this->executeModule($argc, $argv);
            
        }
        
    }
    
    /**
     * Execute module 
     * 
     * @param int $argc Args count
     * @param array $argv Argument list
     * 
     * @throw RuntimeException
     */
    private function executeModule($argc, $argv)
    {
        
        // after filtering will be always as a last array element
        $module = end($this->argsTmp);
        
        if(class_exists($module) === true) {

            // pass original vars in to module construct
            $obj = new $module($argc, $argv);
            
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
        
        CliColors::render($string, CliColors::BG_YELLOW);
        
    }
    
}
