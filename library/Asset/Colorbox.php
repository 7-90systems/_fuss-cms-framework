<?php
    /**
     *  @package fusecms
     *
     *  This class represents the Colorbox asset.
     */
    
    namespace Fuse\Asset;
    
    use Fuse\Asset;
    
    
    class Colorbox extends Asset {
        
        /**
         *  Register the assets JavaScript and CSS files.
         */
        public function register () {
            // Are we in debug mode?
            if (defined ('WP_DEBUG') && WP_DEBUG === true || defined ('SCRIPT_DEBUG') && SCRIPT_DEBUG === true) {
                // Load full versions
                wp_register_script ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/jquery.colorbox.js', array ('jquery'));
            } // if ()
            else {
                // Load minified versions
                wp_register_script ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/jquery.colorbox.min.js', array ('jquery'));
            } // else
            
            wp_register_style ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/example1/colorbox.css');
        } // register ()
        
    } // class Colorbox