<?php

/**
 * Cli Colors
 *
 * FG const prefix holds color code for foreground
 * BG const prefix holds color code for background
 * 
 * @link http://www.if-not-true-then-false.com/2010/php-class-for-coloring-php-command-line-cli-scripts-output-php-output-colorizing-using-bash-shell-colors/
 */

namespace mcommander;

class CliColors {
    
	// Set up shell colors
	const FG_BLACK = ';30';
	const FG_DARK_GREY = ';30';
	const FG_BLUE = ';34';
	const FG_LIGHT_BLUE = ';34';
	const FG_GREEN = ';32';
	const FG_LIGHT_GREEN = ';32';
	const FG_CYAN = ';36';
	const FG_LIGHT_CYAN = ';36';
	const FG_RED = ';31';
	const FG_LIGHT_RED = ';31';
	const FG_PURPLE = ';35';
	const FG_LIGHT_PURPLE = ';35';
	const FG_BROWN = ';33';
	const FG_YELLOW = ';33';
	const FG_LIGHT_GRAY = ';37';
	const FG_WHITE = ';37';
 
	const BG_BLACK = '40';
	const BG_RED = '41';
	const BG_GREEN = '42';
	const BG_YELLOW = '43';
	const BG_BLUE = '44';
	const BG_MAGENTA = '45';
	const BG_CYAN = '46';
	const BG_LIGHT_GRAY = '47';
	
 
	/**
         * 
         * Render coloured string
         * 
         * @param string $string String to be colored
         * @param const $foreground_color [optional] Foregrounf color code
         * @param const $background_color [optional] Background color code
         * @param bool $newLine [optional] Set true if you wish attach end line code '\n'
         * @param const $bold [optional] Set font bold
         */
	static public function render($string, $foreground_color = null, $background_color = null, $newLine = false, $bold = false) 
        {
            
            $bolder = 0;
            $colored_string = "";

            // Check if given foreground color found
            if (isset($foreground_color)) {
                
                if($bold === true) {
                    
                    $bolder = 1;
                    
                } 
                
                $colored_string .= "\033[" . $bolder . $foreground_color . "m";

            }
            // Check if given background color found
            if (isset($background_color)) {

                $colored_string .= "\033[" . $background_color . "m";

            }

            // Add string and end coloring
            $colored_string .=  $string . "\033[0m";

            if($newLine === true) {
                
                print($colored_string . PHP_EOL);
                
            } else {
                
                print $colored_string;
                
            }
                       
	}
 
}
