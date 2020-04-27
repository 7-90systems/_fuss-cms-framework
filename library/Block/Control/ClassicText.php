<?php
    /**
     *  @package fsecms
     *
     *  Set up the classic text control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class ClassicText extends Control {
    
        /**
         * Control name.
         *
         * @var string
         */
        public $name = '';
    
        /**
         * Class constructor.
         *
         * @return void
         */
        public function __construct () {
            parent::__construct ('classic_text', __ ('Classic Text', 'fuse'));
        } // __construct ()
        
        
        
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            foreach (array ('help', 'default') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foreach ()
        } // _registerSettings ()
        
    } // class ClassicText