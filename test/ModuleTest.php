<?php

namespace test;

use mcommander\AbstractCliModule;

class ModuleTest extends AbstractCliModule {
    
    public $test = false;
    
    public function execute() 
    {
    
        $this->warningOutput('Testing complete' . PHP_EOL);
        
    }
    
    protected function loadOptions() 
    {
        
        parent::loadOptions();
        
        $this->defaultOptions[] = array('options' => array('-t', '--test'), 
                                        'callback' => 'testMe', 
                                        'description' => 'Test me');
        
        $this->defaultOptions[] = array('options' => array('-n', '--not-exists'), 
                                        'callback' => 'notExists', 
                                        'description' => 'Test for non existing method');
        
    }
    
    protected function testMe() 
    {
        
        $this->test = true;
        
    }
    
}
