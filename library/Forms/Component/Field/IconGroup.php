<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is an icon group selction field.
     */
    
    namespace Fuse\Forms\Component\Field;
    
    use Fuse\Forms\Component\Field;
    
    
    class IconGroup extends Field {
        
        /**
         *  @var array The options for this field.
         */
        protected $_options;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $name The fields name.
         *  @param string $label The fields label.
         *  @param array $options The options for this field. This is an associive array with the keys being the field value options and the values being the image URL.
         *  @param mixed $value The fields value.
         *  @param array $args The arguments for this field. See the parent
         *  class for valid argument values.
         */
        public function __construct (string $name, string $label, array $options, $value = '', array $args = array ()) {
            parent::__construct ($name, $label, $value, $args);
            
            $this->_options = $options;
        } // __construct ()
        
        
        
        
        /**
         *  Get the options for this field.
         *
         *  @return array The existing set of options.
         */
        public function getOptions () {
            return $this->_options;
        } // getOptions ()
        
        /**
         *  Set the options for this field.
         *
         *  @param array The options to set.
         *
         *  @return Fuse\Forms\Component\Field\Select This select field object.
         */
        public function setOptions (array $options) {
            $this->_options = $options;
            
            return $this;
        } // setOptions ()
        
        
        
        
        /**
         *  Render the field!
         */
        public function render ($output = true) {
            ob_start ();
            $value = $this->getValue ();
            ?>
                <div class="fuse-form-field-icongroup">
                    
                    <?php foreach ($this->_options as $val => $image): ?>
                        <a href="#" class="fuse-form-field-icongroup-image<?php if ($val == $value) echo ' selected'; ?>" data-value="<?php esc_attr_e ($val); ?>">
                            <span class="image" style="background-image: url('<?php echo esc_url ($image); ?>');">&nbsp;</span>
                        </a>
                    <?php endforeach; ?>
                    
                    <input type="hidden" name="<?php esc_attr_e ($this->getName ()); ?>" value="<?php esc_attr_e ($value); ?>" />
                    
                </div>
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
        
    } // class IconGroup