<?php
    /**
     *  @package fusecms
     *
     *  Set up a multi-select control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class MultiSelect extends Control {
    
        /**
         * Select constructor.
         *
         * @return void
         */
        public function __construct() {
            $this->type = 'array';
            
            parent::__construct ('multiselect', __ ('Multi-Select', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name' => 'options',
                'label' => __ ('Choices', 'fuse'),
                'type' => 'textarea_array',
                'default' => '',
                'help' => sprintf (
                    '%s %s<br />%s<br />%s',
                    __ ('Enter each choice on a new line.', 'fuse'),
                    __ ('To specify the value and label separately, use this format:', 'fuse'),
                    _x ('foo : Foo', 'Format for the menu values. option_value : Option Name', 'fuse'),
                    _x ('bar : Bar', 'Format for the menu values. option_value : Option Name', 'fuse')
                ),
                'sanitise' => array ($this, 'sanitizeTextareaAssocArray')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'default',
                'label' => __ ('Default Value', 'fuse'),
                'type' => 'textarea_array',
                'default' => '',
                'help' => __ ('Enter each default value on a new line.', 'fuse'),
                'sanitise' => array ($this, 'sanitiseTextareaArray'),
                'validate' => array ($this, 'validateOptions')
            ));
        } // _registerSettings ()
        
    } // class MutliSelect