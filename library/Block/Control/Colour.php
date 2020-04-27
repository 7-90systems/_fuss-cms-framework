<?php
    /**
     *  @package fusecms
     *
     *  Set up the colour control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Colour extends Control {
    
        /**
         * Text constructor.
         *
         * @return void
         */
        public function __construct () {
            parent::__construct ('colour', __ ('Colour', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         * Register settings.
         *
         * @return void
         */
        public function _registerSettings () {
            foreach (array ('location', 'width', 'help', 'default') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foreach ()
        } // _registerSettings ()
        
    } // class Colour