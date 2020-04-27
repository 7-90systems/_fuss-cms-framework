<?php
    /**
     *  @package fusecms
     *
     *  Set up the email control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Email extends Control {
    
        /**
         * Text constructor.
         *
         * @return void
         */
        public function __construct () {
            parent::__construct ('email', __ ('Email', 'fuse'));
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
                    'type' => 'email',
                    'default' => '',
                    'sanitise' => 'sanitize_email',
            ));
            $this->settings [] = new Setting ($this->settings_config ['placeholder']);
        } // _registerSettings ()
        
    } // class Email