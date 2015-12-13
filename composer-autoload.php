<?php

/* 
 * If you use composer use this file for autoload
 */
foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    
    if(file_exists($file) === true) {
        
        require_once $file;
        
        break;
        
    } else {
        
        die("COMPOSER");
        
    }
    
}
