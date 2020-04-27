<?php
    /**
     *  @package fusecms
     *
     *  Set up our text control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Text extends Control {
    
        /**
         * Text constructor.
         *
         * @return void
         */
        public function __construct () {
            parent::__construct ('text', __ ('Text', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            foreach (array ('location', 'width', 'help', 'default', 'placeholder') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foreach ()
    
            $this->settings [] = new Setting (array (
                'name' => 'maxlength',
                'label' => __ ('Character Limit', 'fuse'),
                'type' => 'number_non_negative',
                'default' => '',
                'sanitise' => array ($this, 'sanitiseNumber')
            ));
        } // _registerSettings ()
        
    } // class Text