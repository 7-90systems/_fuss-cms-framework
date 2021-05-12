<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This class is the base for our form sections. These will be housed within panels and  show the required fields.
     */
    
    namespace Fuse\Admin\SettingsForm;
    
    
    class Section {
        
        /**
         *  @var string The panel ID.
         */
        public $id;
        
        /**
         *  @var string The label for the panel.
         */
        public $label;
        
        /**
         *  @var string The items to be displayed on the panel.
         */
        protected $_items;
        
        /**
         *  @var array The panels arguments.
         */
        protected $_args;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $id The panel ID;
         *  @param string $label The label for this panel
         *  @param array $args The arguments for this panel:
         *      description     The description to go above the fields.
         *      help            The help text to go under the fields.
         */
        public function __construct ($id, $label, $fields = array (), $args = array ()) {
            $this->id = $id;
            $this->label = $label;
            $this->_fields = $fields;
            
            $this->_args = array_merge (array (
                'description' => '',
                'help' => ''
            ), $args);
        } // __consturct ()
        
        
        
        
        /**
         *  Get the HTML code for this section.
         *
         *  @return string The sections HTML code.
         */
        public function getSectionHtml () {
            $class = array (
                'fuse-panel-section'
            );
            
            $html = '<div id="'.esc_attr ($this->id).'" class="'.implode (' ', $class).'">';
            $html.= '  <h4>'.$this->label.'</h4>';
            
            if (strlen ($this->_args ['description']) > 0) {
                $html.= apply_filters ('the_content', $this->_args ['description']);
            } // if ()
            
            foreach ($this->_fields as $field) {
                $html.= $field->render ();
            } // foreach ()
            
            if (strlen ($this->_args ['description']) > 0) {
                $html.= '<p class="fuse-settings-form-help">'.$this->_args ['help'].'</p>';
            } // if ()
            
            $html.= '</div>';
            
            return $html;
        } // getSectionHtml ()
        
        
        
        
        /**
         *  Get the HTML code for thi section.
         *
         *  @return string The sections HTML code;
         */
        public function __toString () {
            return $this->getSectionHtml ();
        } // __toString ()
        
    } // class Section