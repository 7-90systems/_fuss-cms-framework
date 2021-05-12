<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is a normal text field
     */
    
    namespace Fuse\Admin\SettingsForm\Field;
    
    use Fuse\Admin\SettingsForm\Field;
    
    
    class Text extends Field {
        
        /**
         *  Object constructor.
         */
        public function __construct ($name, $value = '', $attributes = array ()) {
            $attributes ['type'] = 'text';
            
            parent::__construct ($name, $value, $attributes);
        } // __construct ()
        
        
        
        
        /**
         *  This function is where we generate and return the fields HTML code.
         *
         *  @return string /the fields HTML code.
         */
        protected function _getFieldHtml () {
            return '<input '.$this->_getAttributeHtml ().' />';
        } // _getFieldHtml ()
        
    } // class Text