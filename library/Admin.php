<?php
    /**
     *  @package fusecms
     *
     *  This class performs our base administration tasks and sets up the admin
     *  pages for the system.
     */
    
    namespace Fuse;
    
    
    class Admin {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            add_action ('admin_init', array ($this, 'rfi_admin_init'));
        } // __construct ()
        
        
        
        
        
        function rfi_admin_init () {
            // Create Setting
            $settings_group = 'rfi';
            $setting_name = 'rfi_post_types';
            register_setting( $settings_group, $setting_name );
        }
        
    } // class Admin