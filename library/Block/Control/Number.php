<?php
    /**
     *  @package fusecms
     *
     *  Set up the number control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Number extends Control {
    
        /**
         * Text constructor.
         *
         * @return void
         */
        public function __construct () {
            $this->type = 'integer';
            
            parent::__construct ('number', __ ('Number', 'fuse'));
        } // __construct ()
    
    
    
    
    
    
        /**
         * Register settings.
         *
         * @return void
         */
        public function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name' => 'default',
                'label' => __ ('Default Value', 'fuse'),
                'type' => 'number',
                'default' => '',
                'sanitise' => function ($value) {
                    return filter_var ($value, FILTER_SANITIZE_NUMBER_INT);
                }
            ));
            $this->settings [] = new Setting ($this->settings_config ['placeholder']);
        } // _registerSettings ()
        
    } // class Number