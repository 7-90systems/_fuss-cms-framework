<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our base button component.
     */
    
    namespace Fuse\Forms\Component;
    
    use Fuse\Html\Element;
    
    
    class Button extends Element {
        
        /**
         *  
         */
        
        /**
         *  Object constructor.
         *
         *  @param string $button_text The text for this button.
         *  @param string $type The button type.
         *  @param array $args The arguments for this button.
         */
        public function __construct ($button_text, $type = 'submit', $args = array ()) {
            $args = array_merge (array (
                'class' => array (
                    'button',
                    'button-primary'
                ),
                'value' => $button_text,
                'type' => $type
            ), $args);
            
            parent::__construct ('input', $args);
        } // __construct ()
        
    } // class Button