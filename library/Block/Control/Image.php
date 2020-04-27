<?php
    /**
     *  @package fusecms
     *
     *  set up the image control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Image extends Control {
    
        /**
         * Text constructor.
         *
         * @return void
         */
        public function __construct() {
            $this->type = 'integer';
            
            parent::__construct ('image', __ ('Image', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            foreach (array ('location', 'width', 'help') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foreach ()
        } // _registerSettings () 
        
        
        
    
        /**
         *  Validates the value to be made available to the front-end template.
         *
         *  @param string $value The value to either make available as a
         *  variable or echoed on the front-end template.
         *  @param bool   $echo Whether this value will be echoed.
         *
         *  @return string|int $value The value to be made available or echoed
         *  on the front-end template, possibly 0 if none found.
         */
        public function validate ($value, $echo) {
            $image_id = intval ($value);
            
            if ($echo) {
                $image = wp_get_attachment_image_src ($image_id, 'full');
                $image_id = !empty ($image [0]) ? $image [0] : '';
            } // else
            
            return $image_id;
        } // validate ()
        
    } // class Image