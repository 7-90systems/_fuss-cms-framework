<?php
    /**
     *  @package fusecms
     *
     *  This is our set up class.
     *
     *  @filter fuse_load_functions_from Set the folder locations to load
     *  function files from.
     *  @filter fuse_register_blocks Add the class names of your own custom
     *  Gutenberg blocks using the Fuse\Editor\Block() class.
     *
     *  @action fuse_before_load_functions Run before function files are loaded.
     *  @action fuse_efter_load_functions Fun after function files are loaded.
     */
    
    namespace Fuse;
    
    
    class Setup {
        
        /**
         *  @var Fuse\Loader The singular instance of the setup class.
         */
        static private $_instance;
        
        
        
        
        /**
         *  Object constructor.
         */
        private function __construct () {
            
            
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
        } // __construct ()
        
        
        
        
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
                    if (strtolower (substr ($file, -9, 9)) != 'index.php') {
                        require_once ($file);
                    } // if ()
                } // foreach ()
            } // foreach ()
            
            do_action ('fuse_after_load_actions');
        } // loadFunctions ()

        
        
        
        /**
         *  Get the single instance of this class.
         */
        static final public function getInstance () {
            if (empty (self::$_instance)) {
                self::$_instance = new Setup ();
            } // if ()
            
            return self::$_instance;
        } // getInstance ()
        
    } // class Setup