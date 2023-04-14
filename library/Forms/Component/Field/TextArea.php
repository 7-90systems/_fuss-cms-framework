<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is a standard text area field.
     */
    
    namespace Fuse\Forms\Component\Field;
    
    use Fuse\Forms\Component\Field;
    
    
    class TextArea extends Field {
        
        /**
         *  Object constructor.
         *
         *  @param string $name The fields name.
         *  @param string $label The fields label.
         *  @param mixed $value The fields value.
         *  @param array $args The arguments for this field. See the parent
         *  class for valid argument values.
         */
        public function __construct ($name, $label, $value = '', $args = array ()) {
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
                'name' => $this->getName ()
            ));
            
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
                <textarea<?php echo fuse_format_attributes ($attributes); ?>><?php echo $this->getValue (); ?></textarea>
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
        
    } // class TextArea