<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our forms Action bar.
     */
    
    namespace Fuse\Forms\Component;
    
    use Fuse\Forms\Container;
    
    
    class ActionBar extends Container {
        
        /**
         *  Object constructor.
         *
         *  @param array $buttons The action buttons to add.
         */
        public function __construct ($buttons = array ()) {
            $this->setItems ($buttons);
            
            $this->class = 'fuse-forms-container fuse-forms-action-bar';
        } // __construct ()
        
    } // class ActionBar ()