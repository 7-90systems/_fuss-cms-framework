<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is our base field class. Use this to create your own fields.
     */
    
    namespace Fuse\Admin\SettingsForm;
    
    
    abstract class Field {
        
        /**
         *  @var string The field name.
         */
        public $name;
        
        /**
         *  @var mixed The forms value.
         */
        public $value;
        
        /**
         *  @var array The field attributes.
         */
        protected $_attributes;
        
        /**
         *  @var array This is a list of attributes for the HTML parser to ignore. You can updates this or over-write it completely in your classes.
         */
        protected $_attribute_ignore_values = array (
            'description',
            'help'
        );
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $name The field name.
         *  @param array $attributes The attributes for this field. Available attributes will change for each field type.
         */
        public function __construct ($name, $value = '', $attributes = array ()) {
            $this->name = $name;
            $this->_attributes = $attributes;
            $this->value = $value;
        } // __construct ()
        
        
        
        
        /**
         *  Render the fields HTML code.
         *
         *  @param bool $output True to output the field or false to return the HTML code.
         *
         *  @return string|NULL The fields HTML or NULL if it is output.
         */
        public function render (bool $output = false) {
            if ($output === true) {
                echo $this->_getFieldHtml ();
            } // if ()
            else {
                return $this->_getFieldHtml ();
            } // else
        } // render ()
        
        
        
        
        /**
         *  Get the attribute HTML code for this field.
         *
         *  @return string The HTML attributes.
         */
        protected function _getAttributeHtml () {
            $atts = array ();
            
            // Set up our defaults
            $this->_attributes ['name'] = $this->name;
            $this->_attributes ['value'] = $this->value;
            
            foreach ($this->_attributes as $key => $val) {
                if (in_array ($key, $this->_attribute_ignore_values) === false) {
                    if (is_array ($val)) {
                        $val = implode (' ', $val);
                    } // if ()
                    
                    if (strlen ($val) > 0) {
                        $atts [] = esc_attr ($key).'="'.esc_attr ($val).'"';
                    } // if ()
                } // if ()
            } // foreach ()
            
            return implode (' ', $atts);
        } // _getAttibuteHtml ()
        
        
        
        
        /**
         *  Get an attribute value for this field.
         *
         *  @param string $name The name of the attibute to get.
         *
         *  @return NUL|mixed Returns a NULL value if the attribute doesn't exist, or returns the value.
         */
        public function __get ($name) {
            $value = NULL;
            
            if (array_key_exists ($name, $this->_attributes)) {
                $value = $this->_attributes [$name];
            } // if ()
            
            return $value;
        } // __get ()
        
        /**
         *  Set an attribute value for this field.
         *
         *  @paran string $name The name of the attribute to set.
         *  @param mixed $value The value to set.
         */
        public function __set ($name, $value) {
            $this->_attributes [$name] = $value;
        } // __set ()
        
        
        
        
        /**
         *  This function is where we generate and return the fields HTML code.
         *
         *  @return string /the fields HTML code.
         */
        abstract protected function _getFieldHtml ();
        
    } // abstract class Field