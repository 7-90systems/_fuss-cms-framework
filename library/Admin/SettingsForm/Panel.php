<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is the base settings form panel class.
     *
     *  Actions:
     *      fuse_before_settings_form_panel_{panel_id}
     *      fuse_after_settings_form_panel_{panel_id}
     */
    
    namespace Fuse\Admin\SettingsForm;
    
    
    class Panel {
        
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
         *      description     The description text shown.
         */
        public function __construct ($id, $label, $items = array (), $args = array ()) {
            $this->id = $id;
            $this->label = $label;
            $this->_items = $items;
            
            $this->_args = array_merge (array (
                'description' => ''
            ), $args);
        } // __construct ()
        
        
        
        
        /**
         *  Return the panels HTML code.
         *
         *  @return string The HTML code for the panel.
         */
        public function getPanelHtml () {
            $html = '<h2>'.$this->label.'</h2>';
            
            if (strlen ($this->_args ['description']) > 0) {
                $html.= apply_filters ('the_content', $this->_args ['description']);
            } // if ()
            
            do_action ('fuse_before_settings_form_panel_'.$this->id);
            
            foreach ($this->_items as $item) {
                $html.= $item.'';
            } // foreach ()
            
            do_action ('fuse_after_settings_form_panel_'.$this->id);
            
            return $html;
        } // getPanelHtml ()
        
        
        
        
        /**
         *  Return the HTML code for this panel.
         *
         *  @return stirng The panels HTML code.
         */
        public function __toString () {
            return $this->getPanelHtml ();
        } // __toString ()
        
    } // class Panel