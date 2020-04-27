<?php
    /**
     *  @package fusecms
     *
     *  Set up our control settings.
     */
    
    namespace Fuse\Block\Control;
    
    
    class Setting {
    
        /**
         *  @var string Setting name (slug).
         */
        public $name = '';
    
        /**
         *  @var string Setting label.
         */
        public $label = '';
    
        /**
         *  @var string Setting type.
         */
        public $type = '';
    
        /**
         *  @var mixed Default value.
         */
        public $default = '';
    
        /**
         *  @var string Help text.
         */
        public $help = '';
    
        /**
         *  @var mixed Sanitising function.
         */
        public $sanitise = '';
    
        /**
         *  @var mixed Validating function.
         */
        public $validate = '';
    
        /**
         *  @var mixed Current value. Null for unset.
         */
        public $value = null;
    
    
    
    
        /**
         *  Object constructor.
         *
         * @param array $args An associative array with keys corresponding to
         * the Option's properties.
         */
        public function __construct ($args = array ()) {
            $settings = array (
                'name',
                'label',
                'type',
                'default',
                'help',
                'sanitise',
                'validate',
                'value'
            );
            
            foreach ($settings as $setting) {
                if (array_key_exists ($setting, $args)) {
                    $this->$setting = $args [$setting];
                } // if ()
            } // foreach ()
        } // __construct ()
    
        /**
         *  Get the current value, using the default if there is none set.
         *
         *  @return mixed
         */
        public function getValue () {
            $value = $this->value;
            
            if (is_null ($value)) {
                $value = $this->default;
            } // if ()
    
            return $value;
        } // getvalue ()
        
    } // class Setting