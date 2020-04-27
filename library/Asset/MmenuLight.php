<?php
    /**
     *  @package fusecms
     *
     *  This class represents the Mmenu Light asset.
     */
    
    namespace Fuse\Asset;
    
    use Fuse\Asset;
    
    
    class MmenuLight extends Asset {
        
        /**
         *  Register the assets JavaScript and CSS files.
         */
        public function register () {
            wp_register_script ('mmenulight', FUSE_BASE_URL.'/assets/external/mmenu-light-master/dist/mmenu-light.js');
            wp_register_style ('mmenulight', FUSE_BASE_URL.'/assets/external/mmenu-light-master/dist/mmenu-light.css');
        } // register ()
        
    } // class MmenuLight