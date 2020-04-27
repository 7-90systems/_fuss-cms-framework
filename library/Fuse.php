<?php
    /**
     *  Set up our global Fuse object.
     */
    
    namespace Fuse;
    
    use Fuse\Layout;
    
    
    class Fuse {
        
        /**
         *  @var Fuse\Fuse The single Fuse object.
         */
        static private $_instance;
        
        
        
        
        /**
         *  @var Fuse\Layout The layout for the page.
         */
        public $layout;
        
        
        
        
        /**
         *  Object constructor.
         */
        private function __construct () {
            $this->layout = new Layout ();
        } // __construct ()
        
        
        
        
        /**
         *  Get the single Fuse\Fuse instance.
         *
         *  @return Fuse\Fuse The singular Fuse object instance.
         */
        final static public function getInstance () {
            if (empty (self::$_instance)) {
                self::$_instance = new Fuse ();
            } // if ()
            
            return self::$_instance;
        } // getInstance ()
        
    } // class Fuse