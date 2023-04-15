<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our set up class.
     *
     *  @filter fuse_load_functions_from Set the folder locations to load
     *  function files from.
     *  @filter fuse_register_blocks Add the class names of your own custom
     *  Gutenberg blocks using the Fuse\Editor\Block() class.
     *
     *  @action fuse_before_load_functions Run before function files are loaded.
     *  @action fuse_efter_load_functions Run after function files are loaded.
     *  @action fuse_init Run after the Fuse system is initialised and is ready.
     */
    
    namespace Fuse;
    
    use Fuse\Traits\Singleton;
    
    
    class Setup {
        
        use Singleton;
        
        
        
        
        /**
         *  Set up our class.
         */
        private function _init () {
            /**
             *  Load our functions
             */
            add_action ('after_setup_theme', array ($this, 'loadFunctions'));
            
            /**
             *  Set up our various additions.
             */
            $setup_theme = new Setup\Theme ();
            
            $posttype_layout = new PostType\Layout ();
            $email_sender = new Setup\EmailSender ();
            
            if (is_admin ()) {
                $admin = new Admin ();
            } // if ()
            
            /**
             *  When we are finished we can call the action related to Fuse.
             */
            do_action ('fuse_init');
        } // _init ()
        
        
        
        
        /**
         *  Load our standard functions by including the files in the
         *  'functions' folder.
         */
        public function loadFunctions () {
            do_action ('fuse_before_load_functions');
            
            // Load core Fuse function files
            $functions_dirs = apply_filters ('fuse_load_functions_from', array (
                FUSE_BASE_URI.DIRECTORY_SEPARATOR.'functions'
            ));
                       
            foreach ($functions_dirs as $dir) {
                $files = glob (trailingslashit ($dir).'*.php');
                
                foreach ($files as $file) {
                    if (basename ($file) != 'index.php') {
                        require_once ($file);
                    } // if ()
                } // foreach ()
            } // foreach ()
            
            do_action ('fuse_after_load_actions');
        } // loadFunctions ()
        
    } // class Setup