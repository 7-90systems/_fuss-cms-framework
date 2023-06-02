<?php
    /**
     *  @package fuse-cms
     *
     *  This class handles our standard plugin and theme update checks.
     */
    
    namespace Fuse;
    
    use Fuse\Traits\Singleton;
    
    
    class Update {
        
        use Singleton;
        
        
        
        
        /**
         *  Initialise our class
         */
        protected function _init () {
            // Set to always check for development environments
            if (defined ('WP_DEBUG') && WP_DEBUG === true) {
                set_site_transient ('update_plugins', null);
                set_site_transient ('update_themes', null);
            } // if ()
            
            $update_themes = new Update\Theme ();
            $update_plugins = new Update\Plugin ();
        } // _init ()
        
    } // class Update