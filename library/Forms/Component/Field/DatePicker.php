<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is a date picker field.
     */
    
    namespace Fuse\Forms\Component\Field;
    
    use Fuse\Forms\Component\Field\Text;
    
    
    class DatePicker extends Text {
        
        /**
         *  Object constructor.
         *
         *  @param string $name The fields name.
         *  @param string $label The fields label.
         *  @param mixed $value The fields value.
         *  @param array $args The arguments for this field.
         */
        public function __construct ($name, $label, $value = '', $args = array ()) {
            if (array_key_exists ('class', $args)) {
                if (is_array ($args ['class'])) {
                    $args ['class'][]= 'fuse-datepicker';
                } // if ()
                else {
                    $args ['class'].= ' fuse-datepicker';
                } // else
            } // if ()
            else {
                $args ['class'] = 'fuse-datepicker';
            } // else
            
            parent::__construct ($name, $label, $value, $args);
        } // __construct ()
        
        
        
        
        /**
         *  Render the field!
         *
         *  @param bool $render True to render the field, or false to return the
         *  HTML code.
         *
         *  @return string Returns the groups HTML code.
         */
        public function render ($output = true) {
            $attributes = array_merge ($this->_args, array (
                'id' => $this->getId (),
                'name' => $this->getName (),
                'type' => 'text',
                'value' => $this->_value
            ));
            
            $alt_atts = $attributes;
            $alt_atts ['name'] = 'fuse_datepicker_'.$alt_atts ['name'];
            $alt_atts ['id'] = 'fuse_datepicker_'.$alt_atts ['id'];
            
            unset ($attributes ['class']);
            $attributes ['type'] = 'hidden';
            
            if (strlen ($this->_value) > 0) {
                $alt_atts ['value'] = date ('j F Y', strtotime ($this->_value));
            } // if ()
            
            if (array_key_exists ('required', $attributes)) {
                if ($attributes ['required'] === true) {
                    $attributes ['required'] = 'required';
                } // if ()
                else {
                    unset ($attributes ['required']);
                } // else
            } // if ()
            
            ob_start ();
            ?>
                <input<?php echo fuse_format_attributes ($attributes); ?> />
                <input<?php echo fuse_format_attributes ($alt_atts); ?> />
            <?php
            $html = ob_get_contents ();
            ob_end_clean ();
            
            if ($output === true) {
                echo $html;
            } // if ()
            else {
                return $html;
            } // else
        } // render ()
        
    } // class DatePicker