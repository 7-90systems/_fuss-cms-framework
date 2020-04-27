<?php
    /**
     *  @package fusecms
     *
     *  This is a block field.
     */
    
    namespace Fuse\Block;
    
    
    class Field {
    
        /**
         * @var string Field name (slug).
         */
        public $name = '';
    
        /**
         * @var string Field label.
         */
        public $label = '';
    
        /**
         * @var string Field control type.
         */
        public $control = 'text';
    
        /**
         * @var string Field variable type.
         */
        public $type = 'string';
    
        /**
         * @var int Field order.
         */
        public $order = 0;
    
        /**
         * @var array Field settings.
         */
        public $settings = array ();
    
    
    
    
        /**
         *  Object constructor.
         *
         *  @param array $config An associative array with keys corresponding to
         *  the Field's properties.
         */
        public function __construct ($config = array ()) {
            $this->fromArray ($config);
        } // __construct ()
    
    
    
    
        /**
         *  Get field properties as an array, ready to be stored as JSON.
         *
         *  @return array
         */
        public function toArray () {
            $config = array (
                'name' => $this->name,
                'label' => $this->label,
                'control' => $this->control,
                'type' => $this->type,
                'order' => $this->order
            );
    
            $config = array_merge (
                $config,
                $this->settings
            );
    
            // Handle the sub-fields setting used by the Repeater.
            if (isset ($this->settings ['sub_fields'])) {
                foreach ($this->settings ['sub_fields'] as $key => $field) {
                    $config ['sub_fields'][$key] = $field->toArray ();
                } // foreach ()
            } // if ()
    
            return $config;
        } // toArray ()
    
        /**
         *  Set field properties from an array, after being stored as JSON.
         *
         *  @param array $config An array containing field parameters.
         */
        public function fromArray ($config) {
            if (isset ($config ['name'])) {
                $this->name = $config ['name'];
            } // if ()
            
            if (isset ($config ['label'])) {
                $this->label = $config ['label'];
            } // if ()
            
            if (isset ($config ['control'])) {
                $this->control = $config ['control'];
            } // if ()
            
            if (isset ($config ['type'])) {
                $this->type = $config ['type'];
            } // if ()
            
            if (isset ($config ['order'])) {
                $this->order = $config ['order'];
            } // if ()
            
            if (isset ($config ['settings'])) {
                $this->settings = $config ['settings'];
            } // if ()
    
            if (!isset ($config ['type'])) {
                $control_class_name = 'Fuse\\Block\\Controls\\';
                $control_class_name.= ucwords ($this->control, '\\');
                
                if (class_exists ($control_class_name)) {
                    /**
                     * An instance of the control, to retrieve the correct type.
                     *
                     * @var Control_Abstract $control_class
                     */
                    $control_class = new $control_class_name ();
                    $this->type = $control_class->type;
                } // if ()
            } // if ()
    
            // Add any other non-default keys to the settings array.
            $field_defaults = array (
                'name',
                'label',
                'control',
                'type',
                'order',
                'settings'
            );
            $field_settings = array_diff (array_keys ($config), $field_defaults);
    
            foreach ($field_settings as $settings_key) {
                $this->settings [$settings_key] = $config [$settings_key];
            } // foreach ()
    
            // Handle the sub-fields setting used by the Repeater.
            if (isset ($this->settings ['sub_fields'])) {
                /**
                 * Recursively loop through sub-fields.
                 */
                foreach ($this->settings ['sub_fields'] as $key => $field) {
                    $this->settings ['sub_fields'][$key] = new Field ($field);
                } // foreach ()
            } // if ()
        } // fromArray ()
    
    
    
    
        /**
         *  Return the value with the correct variable type.
         *
         *  @param mixed $value The value to typecast.
         *
         *  @return mixed
         */
        public function castValue ($value) {
            switch ($this->type) {
                case 'string':
                    $value = strval ($value);
                    break;
                case 'textarea':
                    $value = strval ($value);
                    
                    if (isset ($this->settings ['new_lines'])) {
                        if ('autop' === $this->settings ['new_lines']) {
                            $value = wpautop ($value);
                        } // if ()
                        if ('autobr' === $this->settings ['new_lines']) {
                            $value = nl2br ($value);
                        } // if ()
                    } // if ()
                    
                    break;
                case 'boolean':
                    if (1 === $value) {
                        $value = true;
                    } // if ()
                    else {
                        $value = false;
                    } // else
                    
                    break;
                case 'integer':
                    $value = intval ($value);
                    break;
                case 'array':
                    if (!$value) {
                        $value = array ();
                    } // if ()
                    else {
                        $value = (array) $value;
                    } // else
                    
                    break;
            } // switch ()
    
            return $value;
        } // castValue ()
    
        /**
         *  Gets the field value as a string.
         *
         *  @param mixed $value The field value.
         *
         *  @return string $value The value to echo.
         */
        public function castValueToString ($value) {
            if (is_array ($value)) {
                $value = implode (', ', $value);
            } // if ()
            elseif (true === $value) {
                $value = __ ('Yes', 'fuse');
            } // elseif ()
            elseif (false === $value) {
                $value = __ ('No', 'fuse');
            } // elseif ()
    
            return strval ($value);
        } // castValuetoSTring ()
        
    } // class Field