<?php

/**
 * This example show the simplest usage of the library
 *
 * @author Wojciech Brozyna <wojciech.brozyna@gmail.com>
 */

namespace examples\SimpleModule;

use mcommander\AbstractCliModule;

class SimpleModule extends AbstractCliModule {
    
    /**
     * This is mandatory function which needs extends AbstractioCliModule
     */
    public function execute()
    {
        
        // PHP_EOL doest the same thing as "\n"
        
        //standard output, nothing special
        $this->output("Hello World!" . PHP_EOL);
        
        $this->successOutput('Hurra! This is my first CLI module' . PHP_EOL);
        
        $this->warningOutput('Warning have yellow colour' . PHP_EOL);
        
        $this->errorOutput('Warning have red colour'  .PHP_EOL);
        
    }
    
}
