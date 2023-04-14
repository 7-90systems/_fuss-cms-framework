<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is a standard email field.
     */
    
    namespace Fuse\Forms\Component\Field;
    
    use Fuse\Forms\Component\Field\Text;
    
    
    class Email extends Text {
        
        /**
         *  Object constructor.
         *
         *  @param string $name The fields name.
         *  @param string $label The fields label.
         *  @param mixed $value The fields value.
         *  @param array $args The arguments for this field. See the parent
         *  class for valid argument values.
         */
        public function __construct ($name, $label, $value = '', $args = array ()) {
            parent::__construct ($name, $label, $value, $args);
            
            $this->_args ['type'] = 'email';
        } // __construct ()
        
    } // class Email