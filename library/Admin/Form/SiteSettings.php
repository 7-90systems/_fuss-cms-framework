<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is our main website settings form.
     */
    
    namespace Fuse\Admin\Form;
    
    use Fuse\Admin\SettingsForm;
    
    
    class SiteSettings extends SettingsForm {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            $panels = array (
                new SettingsForm\Panel ('email', __ ('Email', 'fuse'), array ()),
                new SettingsForm\Panel ('google', __ ('Google API', 'fuse'), array ())
            );
            
            parent::__construct ('fuse_site_settings', $panels, array (
                'submit_button' => __ ('Save Fuse CMS Settings', 'fuse')
            ));
        } // __construct ()
        
    } // class SiteSettings