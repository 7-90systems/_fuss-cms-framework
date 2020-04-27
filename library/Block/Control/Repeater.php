<?php
    /**
     *  @package fusecms
     *
     *  Set up our repeater control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Repeater extends Control {
    
        /**
         * Repeater constructor.
         */
        public function __construct() {
            $this->type = 'object';
            
            parent::__construct ('repeater', __ ('Repeater', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name' => 'min',
                'label' => __ ('Minimum Rows', 'fuse'),
                'type' => 'number_non_negative',
                'sanitise' => array ($this, 'sanitiseNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'max',
                'label' => __ ('Maximum Rows', 'fuse'),
                'type' => 'number_non_negative',
                'sanitise' => array ($this, 'sanitiseNumber')
            ));
        } // _registerSettings ()
    
    
    
    
        /**
         *  Remove empty placeholder rows.
         *
         *  @param mixed $value The value to either make available as a variable or echoed on the front-end template.
         *  @param bool  $echo Whether this will be echoed.
         *
         *  @return mixed $value The value to be made available or echoed on the front-end template.
         */
        public function validate ($value, $echo) {
            if (isset ($value ['rows'])) {
                foreach ($value ['rows'] as $key => $row) {
                    unset ($value ['rows'][$key]['']);
                    unset ($value ['rows'][$key][0]);
                } // foreach ()
            } // if ()
    
            if ($echo === true && defined ('WP_DEBUG') && WP_DEBUG === true) {
                $value = __( 'Please use repeater functions to display repeater fields in your template.', 'fuse');
            } // if ()
    
            return $value;
        } // validate ()
        
    } // class Repeater