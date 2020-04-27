<?php
    /**
     *   @package fusecms
     *
     *   Set up a checkbox control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Checkbox extends Control {
    
        /**
         * Checkbox constructor.
         *
         * @return void
         */
        public function __construct() {
            $this->type = 'boolean';
            
            parent::__construct ('checkbox', __ ('Checkbox', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array ( 
                'name' => 'default',
                'label' => __ ('Default Value', 'fuse'),
                'type' => 'checkbox',
                'default' => '0',
                'sanitise' => array ($this, 'sanitizeCheckbox')
            ));
        } // _registerSettings ()
        
    } // class Checkbox