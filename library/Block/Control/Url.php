<?php
    /**
     * @package fusecms
     *
     * Set up a URL control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    /**
     * Class Text
     */
    class Url extends Control {
    
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('url', __ ('URL', 'fuse'));
        } // __construct ()
        
        
        
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name'     => 'default',
                'label'    => __ ('Default Value', 'fuse'),
                'type'     => 'url',
                'default'  => '',
                'sanitize' => 'esc_url_raw'
            ));
            $this->settings [] = new Setting ($this->settings_config ['placeholder']);
        } // _registerSettings ();
        
    } // class Url