<?php
    /**
     *  @package fusecms
     *
     *  This class represents the BX Slider asset.
     */
    
    namespace Fuse\Asset;
    
    use Fuse\Asset;
    
    
    class BxSlider extends Asset {
        
        /**
         *  Register the assets JavaScript and CSS files.
         */
        public function register () {
            // Are we in debug mode?
            if (defined ('WP_DEBUG') && WP_DEBUG === true || defined ('SCRIPT_DEBUG') && SCRIPT_DEBUG === true) {
                // Load full versions
                wp_register_script ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.js', array ('jquery'));
                wp_register_style ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.css');
            } // if ()
            else {
                // Load minified versions
                wp_register_script ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.min.js', array ('jquery'));
                wp_register_style ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.min.css');
            } // else
        } // register ()
        
    } // class BxSlider