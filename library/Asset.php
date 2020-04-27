<?php
    /**
     *  @package fusecms
     *
     *  This is the base for our asset classes.
     *
     *  Assets are 3rd arty of external resources, like BX Slider, Colorbox,
     *  etc.
     */
    
    namespace Fuse;
    
    
    abstract class Asset {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            
        } // __construct ()
        
        
        
        
        /**
         *  This function is used to register the JavaScript and CSS files that
         *  are part of this asset.
         *
         *  To do this the files need to be added into the $_css and
         *  $_javascript arrays of this class.
         */
        abstract public function register ();
        
    } // abstract class Asset