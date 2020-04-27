<?php
    /**
     *  @package fusecms
     *
     *  Set up the toggle control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Toggle extends Control {
    
        /**
         *  Object constructor.
         */
        public function __construct() {
            $this->type = 'boolean';
            
            parent::__construct ('toggle', __ ('Toggle', 'fuse'));
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
                'sanitise' => array ($this, 'sanitiseCheckbox')
            ));
        } // _registerSettings ()
        
    } // class Toggle