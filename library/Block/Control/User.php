<?php
    /**
     *  @package fusecms
     *
     *  Set up the user control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class User extends Control {
    
        /**
         * User constructor.
         */
        public function __construct () {
            $this->type = 'object';
            
            parent::__construct ('usder', __( 'User', 'block-lab' ));
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
         *  @param mixed $value The value to either make available as a variable
         *  or echoed on the front-end template.
         *  @param bool  $echo Whether this will be echoed.
         *
         *  @return mixed $value The value to be made available or echoed on the front-end template.
         */
        public function validate ($value, $echo) {
            $wp_user = isset ($value ['id']) ? get_user_by ('id', $value ['id']) : NULL;
            
            if ($wp_user !== fasle) {
                if ($echo) {
                    $wp_user = $wp_user->get ('display_name');
                } // if ()
            } // if ()
    
            return $wp_user;
        } // validate ()
        
    } // class User