<?php
    /**
     *  @package fusecms
     *
     *  Set up our rich text control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class RichText extends Control {
    
        /**
         * Class constructor.
         *
         * @return void
         */
        public function __construct() {
            parent::__construct ('rich_text', __ ('Rich Text', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            foreach (array ('help', 'default', 'placeholder') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foreach ()
        } // _registerSettings ()
    
    
    
    
        /**
         *  Validates the value to be made available to the front-end template.
         *
         *  @param mixed $value The value to either make available as a variable
         *  or echoed on the front-end template.
         *  @param bool  $echo Whether this will be echoed.
         *
         *  @return mixed $value The value to be made available or echoed on the
         *  front-end template.
         */
        public function validate ($value, $echo) {
            $new_value = '';
    
            // If there's no text entered, Rich Text saves '<p></p>', so instead return ''.
            if ('<p></p>' != $value) {
                $new_value = wpautop ($value);
            }// if ()
    
            return $new_value;
        } // validate ()
        
    } // class RichText