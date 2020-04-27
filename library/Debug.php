<?php
    /**
     *  @package fusecms
     *
     *  This class performs our debugging functions.
     */
    
    namespace Fuse;
    
    
    class Debug {
        
        /**
         *  Block direct instantiation.
         */
        private function __construct () {
            // Blocked
        } // __construct ()
        
        
        
        
        /**
         *  Output a debug message.
         *
         *  Output is only given if WP_DEBUG is set to true.
         *
         *  @param mixed $value The value to output.
         *  @param string $title A title for this debug message.
         *  @param bool $die True to die on output or false to continue
         *  operations. Defaults to false.
         */
        static public function dump ($value, $title = '', $die = false) {
            // Only output if WP_DEBUG is true
            if (defined ('WP_DEBUG') && WP_DEBUG === true) {
                echo '<pre style="background: #FFFFFF; color: #000000; border: 2px solid #000000; padding: 20px; font-size: 14px;">';
                
                if (strlen ($title) > 0) {
                    echo $title.':'.PHP_EOL.PHP_EOL;
                } // if ()
                
                var_export ($value);
                
                echo '</pre>';
                
                if ($die === true) {
                    wp_die ();
                } // if ()
            } // if ()
        } // dump ();
        
    } // class Debug