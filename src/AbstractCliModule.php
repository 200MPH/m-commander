<?php

/**
 * Abstract Cli Module
 * Extend this class to create your own module
 * See in to ../examples folder so you will find some interesting solutions
 *
 * @author Wojciech Brozyna <wojciech.brozyna@gmail.com>
 */

namespace mcommander;

use ReflectionObject;

abstract class AbstractCliModule {
    
    /**
     * Email address for notification
     * Set email address in your child class if you wish to receive notifications
     * 
     * @var string
     */
    public $email = null;
    
    /**
     * Notification subject (email subject)
     * Set it in you child class
     * 
     * @var string
     */
    public $notificationSubject = 'M-Commander Notification';
    
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
    protected $verbose = false;
    
    /**
     * Write output file
     * 
     * @var bool|string
     */
    private $writeOutputFile = false;
    
    /**
     * Lock folder
     * 
     * @var string
     */
    private $lockFile = '/tmp/';
    
    /**
     * @var ReflectionObject
     */
    private $reflection;
    
    /**
     * Disable email notification
     * 
     * @var bool
     */
    private $notify = true;
    
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
        
        $this->reflection = new ReflectionObject( $this );
        
        $this->lockFile .= $this->reflection->getShortName() . '.lock';
               
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
     * Send email notification
     * 
     * @param const $type Notify::SUCCESS | Notify::ERROR | Notify::INFO
     * @param string $message Message to be send. HTML code accepted
     * 
     * @return bool
     */
    protected function notify($type, $message)
    {
        
        if($this->notify === true && empty($this->email) === false) {
            
            $notify = new Notify();
            
            $notify->setEmail($this->email);
            
            $notify->setSubject($this->notificationSubject);
            
            $notify->setMessage($message);
            
            return $notify->send($type);
            
        }
        
        return false;
        
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
     * Lock current process
     * 
     * @return void
     */
    protected function lock()
    {
        
        file_put_contents($this->lockFile, "". getmypid() ."@".date('Y-m-d G:i:s')."");
        
        $this->warningOutput('Process '. getmypid() . ' locked at ' . date('Y-m-d G:i:s') . PHP_EOL);
                
    }
    
    /**
     * Disable notification
     * 
     * @return void
     */
    protected function disableNotification()
    {
        
        $this->notify = false;
        
        $this->output('Notification ');
        $this->warningOutput('OFF' . PHP_EOL);
        
    }
    
    /**
     * Check if process is locked and if so, display message.
     * 
     * @return bool|array False when not locked or [0] = PID [1] = Lock timestamp
     */
    public function isLocked()
    {
        
        if(is_file($this->lockFile) === true) {
            
            $arr = $this->parseLockString();
            
            $this->lockNotify();
            
            return $arr;
            
        } else {
            
            return false;
            
        }
        
    }
    
    /**
     * Unlock process
     * 
     * @return void
     */
    public function unlock()
    {
        
        if(is_file($this->lockFile) === true) {
            
            $arr = $this->parseLockString();
            
            unlink($this->lockFile);
            
            $this->successOutput("Process {$arr[0]} unlocked (Locked at {$arr[1]})" . PHP_EOL);
            
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
                                        'description' => "Write output in to file. Eg ./m-commander 'myNamespace\MyModule' -w /home/user/test.log");
        
        $this->defaultOptions[] = array('options' => array('-l', '--lock'), 
                                        'callback' => 'lock', 
                                        'description' => 'Lock module process. Will not let you run another instance of this same module until current is finished. However you can execute script for another module.');
        
        $this->defaultOptions[] = array('options' => array('--disable-notification'), 
                                        'callback' => 'disableNotification', 
                                        'description' => 'Disable email notification');
        
    }
    
    /**
     * Save output in to file
     * 
     * @param string $string
     */
    final protected function saveOutput($string)
    {
        
        if($this->writeOutputFile !== false) {
            
            file_put_contents($this->writeOutputFile, $string, FILE_APPEND);
            
        }
        
    }
    
    /**
     * Setup internal arguments options
     * Each module might have different options.
     * 
     * @return void
     */
    final public function setupOptions()
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

        if(@file_put_contents($this->writeOutputFile, '') === false) {

            // looks like we can't create the file
            throw new \RuntimeException('File is not writable. Check filename and permissions', CliCodes::OPT_FILE_PER_ERR);

        }
        
    }
    
    /**
     * Parse lock string
     * 
     * @return bool|array False when not locked or [0] = PID [1] = Lock timestamp
     */
    final private function parseLockString()
    {
        
        $line = file_get_contents($this->lockFile);
            
        $arr = explode('@', trim($line));
        
        return $arr;
        
    }
    
    /**
     * Send email notification
     * 
     * @return void
     */
    final private function lockNotify()
    {
        
        $lockData = $this->parseLockString();
        $this->notificationSubject = "Process #{$lockData[0]} locked!";

        $msg = "Process {$lockData[0]} locked at: {$lockData[1]} \n";
        $msg .= "This is happens when: \n";
        $msg .= "\t 1. Current process is not finished yet and another instance of the same job is started. \n";
        $msg .= "\t 2. Previous instance crashed and left lock file. \n";
        $msg .= "\nUnlock instruction: \n";
        $msg .= "\t 1. Make sure that unlock process is safe \n";
        $msg .= "\t 2. Remove lock file {$this->lockFile} (note that file name is various)\n";

        $this->notify(Notify::ERROR, $msg);
        
    }
}
