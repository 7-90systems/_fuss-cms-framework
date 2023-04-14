<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is a normal text field
     */
    
    namespace Fuse\Admin\SettingsForm\Field;
    
    use Fuse\Admin\SettingsForm\Field;
    
    
    class Email extends Field\Text {
        
        /**
         *  Object constructor.
         */
        public function __construct ($name, $value = '', $attributes = array ()) {
            parent::__construct ($name, $value, $attributes);
            
            $this->_attributes ['type'] = 'email';
        } // __construct ()
        
    } // class Email