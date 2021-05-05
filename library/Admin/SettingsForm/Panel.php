<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is the base settings form panel class.
     */
    
    namespace Fuse\Admin\SettingsForm;
    
    
    class Panel {
        
        /**
         *  @var string The panel ID.
         */
        
        /**
         *  @var string The label for the panel.
         */
        public $label;
        
        /**
         *  @var string The items to be displayed on the panel.
         */
        protected $_items;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $id The panel ID;
         *  @param string $label The label for this panel
         */
        public function __construct ($id, $label, $items = array ()) {
            $this->id = $id;
            $this->label = $label;
        } // __construct ()
        
    } // class Panel