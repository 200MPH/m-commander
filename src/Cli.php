<?php

/**
 * Command line parser and execute automation code
 * 
 * This class take module name as an argument and try to execute it if module class found.
 * Each module class need to be located in CLIModule/Controller
 * Each module class need extends AbstractCliModule() and implement execute() method
 * 
 * Each module might have different options so typing 
 * php cli.php mod1 -h
 * might give you different output than
 * php cli.php mod2 -h
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
        
        $this->filter();
        
        $this->isModuleProvided();
        
        // after filtering will be always as a last array element
        $module = end($this->argsTmp);
        
        // try to execute module if provided
        if($module !== false) {
            
            $this->execute($argc, $argv);
            
        }
        
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
     * Execute module 
     * 
     * @param int $argc Args count
     * @param array $argv Argument list
     * 
     * @throw RuntimeException
     */
    private function execute($argc, $argv)
    {
        
        // after filtering will be always as a last array element
        $module = end($this->argsTmp);
            
        $namespace = "app\CLIModule\\$module\Controller\\$module";

        if(class_exists($namespace) === true) {

            // pass original vars in to module construct
            $obj = new $namespace($argc, $argv);

            call_user_func(array($obj, 'execute'));

        } else {

            throw new \RuntimeException("Module '{$module}' not found", CliCodes::MOD_NOT_FOUND);

        }
        
    }
    
}
