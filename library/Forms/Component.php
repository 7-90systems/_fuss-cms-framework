<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is the base for all of our component classes.
     */
    
    namespace Fuse\Forms;
    
    
    abstract class Component {
        
        /**
         *  @var array The arguments for this component.
         */
        protected $_args;
        
        /**
         *  @var array This array holds our conditions.
         */
        protected $_conditions;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param array $args The arguments for this component.
         */
        public function __construct (array $args) {
            $conditions = array ();
            
            if (array_key_exists ('conditions', $args)) {
                $conditions = $args ['conditions'];
                unset ($args ['conditions']);
            } // if ()
            
            $this->_args = $args;
            $this->setConditions ($conditions);
        } // __construct ()
        
        
        
        
        /**
         *  Get the values for this component. This will be returned as an
         *  associative array with field names as the keys and the values as...
         *  well values!
         *
         *  return array the values.
         */
        abstract public function getValues ();
        
        
        
        
        /**
         *  Set the conditions for the display of this component.
         *
         *  @param array $conditions The conditions to set for this component.
         *
         *  @return Fuse\Forms\Component This component object.
         */
        public function setConditions (array $conditions) {
            $this->_conditions = array ();
            
            foreach ($conditions as $condition) {
                $field_id = $value = '';
                $comparison = '=';
                
                if (array_key_exists ('field_id', $condition)) {
                    $field_id = $condition ['field_id'];
                } // if ()
                
                if (array_key_exists ('value', $condition)) {
                    $value = $condition ['value'];
                } // if ()
                
                if (array_key_exists ('comparison', $condition)) {
                    $comparison = $condition ['comparison'];
                } // if ()
                
                $this->setCondition ($field_id, $value, $comparison);
            } // foreach ()
            
            return $this;
        } // setConditions ()
        
        /**
         *  Set a condtion for this field.
         *
         *  @param string $field_id The ID of the field that we are checking.
         *  @param mixed $value The value that we are checking for.
         *  @param string $comparison This is how we compare the fields value.
         *  This can be:
         *      = / equal                   - Field value equals given value
         *      ! / not                     - Field value is not given value
         *      > / greater than            - Field value is greater than given value
         *      < / less than               - Field value is less than given value
         *      in (for array values)       - Field value is in the given array
         *      not in (for array values)   - Field value is not in the given array
         *      any                         - Field has any non-empty value
         *      empty                       - Field has an empty value
         *
         *  @return Fuse\Forms\Component This component.
         */
        public function setCondition (string $field_id, $value, string $comparison = '=') {
            $this->_conditions [] = array (
                'field' => $field_id,
                'value' => $value,
                'comparison' => $comparison
            );
            
            $this->_setupConditions ();
            
            return $this;
        } // setCondition ()
        
        
        
        
        /**
         *  Set up our conditions.
         */
        protected function _setupConditions () {
            if (count ($this->_conditions) > 0) {
                $this->_args ['data-conditions'] = json_encode ($this->_conditions);
            } // if ()
            else {
                unset ($this->_args ['data-conditions']);
            } // else
        } // _seetupConditions ()
        
    } // abstract class Component