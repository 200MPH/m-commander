<?php

namespace test;

use mcommander\Cli;

class TestCli extends \PHPUnit_Framework_TestCase {
    
    /**
     * Cli object
     * 
     * @var Cli
     */
    private $cli;
    
    public function setUp() {
        
        $this->cli = new Cli();
        
        $this->cli->testing = true;
        
    }
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     *
     * @link https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
    */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        
       $reflection = new \ReflectionClass(get_class($object));
       $method = $reflection->getMethod($methodName);
       $method->setAccessible(true);

       return $method->invokeArgs($object, $parameters);
       
    }

    /**
     * Testing test :)
     */
    public function testTest() 
    {
        
        $foo = true;
        $this->assertTrue($foo);
        
    }
        
    public function testIsHelpNeededTrue()
    {
        
        $this->cli->despatch(2, array('this/path/is/arg/as/wel', '-h'));
        
        $status = $this->invokeMethod($this->cli, 'isHelpNeeded');

        $this->assertTrue($status);
        
    }
    
    public function testIsHelpNeededArg2True()
    {
        
        $this->cli->despatch(2, array('this/path/is/arg/as/wel', '--help'));
        
        $status = $this->invokeMethod($this->cli, 'isHelpNeeded');

        $this->assertTrue($status);
        
    }
    
    public function testIsHelpNeededWhenHProvidedAndMoreOtherArgsFalse()
    {
        
        $this->setExpectedException('RuntimeException');
        
        $this->cli->despatch(3, array('this/path/is/arg/as/wel', '-h', 'test\ModuleTest', 'else'));
        
    }
    
    public function testIsHelpNeededWhenOtherArgsProvidedFalse()
    {
        
        $this->cli->despatch(3, array('this/path/is/arg/as/wel', 'test\ModuleTest', 'else'));
        
        $status = $this->invokeMethod($this->cli, 'isHelpNeeded');
        
        $this->assertFalse($status);
        
    }
    
    public function testIsModuleProvidedTrue()
    {
        
        $this->cli->despatch(3, array('this/path/is/arg/as/wel', 'test\ModuleTest'));
        
        $status = $this->invokeMethod($this->cli, 'isModuleProvided');
        
        $this->assertTrue($status);
        
    }
    
    public function testIsModuleProvidedFalse()
    {
        
        $this->setExpectedException('RuntimeException');
        
        $this->cli->despatch(3, array());
        
    }
    
}
