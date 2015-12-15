<?php

/**
 * Example how to customize output method
 *
 * @author wojciech.brozyna@gmail.com
 */

namespace examples\mcommander\CustomOutput;

use mcommander\AbstractCliModule;
use mcommander\CliColors;

class CustomOutput extends AbstractCliModule {
    
    /**
     * Display some string
     */
    protected function execute() 
    {
     
        $this->successOutput('I changed success output');
        
    }
    
    /**
     * Oveloaded parent function
     * @param string $string
     */
    protected function successOutput($string) 
    {
     
        CliColors::render($string, CliColors::BG_BLUE, CliColors::FG_WHITE, true);
        
    }
    
}
