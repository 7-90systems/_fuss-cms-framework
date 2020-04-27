<?php
    /**
     *  @package fusecms
     *
     *  Set up a textarea control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Textarea extends Control {
    
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('textarea', __ ('Textarea', 'fuse'));
        }
        
        
        
        
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            foreach (array ('location', 'width', 'help') as $setting) {
                $this->settings [] = new Setting ($this->settings_config [$setting]);
            } // foraech ()
    
            $this->settings [] = new Setting (array (
                'name' => 'default',
                'label' => __ ('Default Value', 'fuse'),
                'type' => 'textarea',
                'default' => '',
                'sanitize' => 'sanitize_textarea_field'
            ));
            $this->settings [] = new Setting ($this->settings_config ['placeholder']);
            $this->settings [] = new Setting (array (
                'name' => 'maxlength',
                'label' => __ ('Character Limit', 'fuse'),
                'type' => 'number_non_negative',
                'default' => '',
                'sanitize' => array ($this, 'sanitizeNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'number_rows',
                'label' => __ ('Number of Rows', 'fuse'),
                'type' => 'number_non_negative',
                'default' => 4,
                'sanitize' => array ($this, 'sanitizeNumber')
            ));
            $this->settings [] = new Setting (array (
                'name' => 'new_lines',
                'label' => __ ('New Lines', 'fuse'),
                'type' => 'new_line_format',
                'default' => 'autop',
                'sanitize' => array ($this, 'sanitiseNewLineFormat')
            ));
        } // _registerSettings ()
    
        
        
        
        /**
         *  Renders a <select> of new line rendering formats.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsNew_line_format ($setting, $name, $id) {
            $this->renderSelect ($setting, $name, $id, $this->_getNewLineFormats ());
        } // renderSettingsNew_line_format ()
    
        /**
         *  Sanitize the new line format, to ensure that it's valid.
         *
         *  @param string $value The format to sanitize.
         *
         *  @return string|null The sanitized rest_base of the post type, or null.
         */
        public function sanitiseNewLineFormat ($value) {
            $new_value = NULL;
            
            if (is_string ($value) && array_key_exists ($value, $this->_getNewLineFormats ())) {
                $new_value = $value;
            } // if ()
            
            return $new_value;
        } // sanitiseNewLineFormat ()
    
    
    
    
        /**
         * Gets the new line formats.
         *
         * @return array {
         *     An associative array of new line formats.
         *
         *     @type string $key    The option value to save.
         *     @type string $label  The label.
         * }
         */
        protected function _getNewLineFormats () {
            $formats = array (
                'autop' => __ ('Automatically add paragraphs', 'fuse'),
                'autobr' => __ ( 'Automatically add line breaks', 'fuse'),
                'none' => __ ( 'No formatting', 'fuse') 
            );
            
            return $formats;
        } // _getNewLineFormats ()
        
    } // class Textarea