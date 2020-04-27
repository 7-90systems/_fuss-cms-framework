<?php
    /**
     *  @package fusecms
     *
     *  Set up the range control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Range extends Control {
    
        /**
         * Range constructor.
         *
         * @return void
         */
        public function __construct() {
            $this->type = 'integer';
            
            parent::__construct ('range', __ ('Range', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name' => 'min',
                'label' => __ ('Minimum Value', 'fuse'),
                'type' => 'number',
                'default' => '',
                'sanitise' => array ($this, 'sanitiseNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'max',
                'label' => __ ('Maximum Value', 'fuse'),
                'type' => 'number',
                'default' => '',
                'sanitize' => array ($this, 'sanitizeNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'step',
                'label' => __ ('Step Size', 'fuse'),
                'type' => 'number_non_negative',
                'default'  => 1,
                'sanitise' => array ($this, 'sanitizeNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'default',
                'label' => __ ('Default Value', 'fuse'),
                'type' => 'number',
                'default' => '',
                'sanitise' => array ($this, 'sanitizeNumber')
            ));
        } // _registerSettings ()
        
    } // class Range