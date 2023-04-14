<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our meta box container.
     */
    
    namespace Fuse\Forms\Container;
    
    use Fuse\Forms\Container;
    
    
    class MetaBox extends Container {
        
        /**
         *  Object constructor.
         *
         *  @param array $items The items to go inside this container. This
         *  should be an associative array so that items can be added before or
         *  after by referencing the items ID (key).
         *  @param array $args The arguments for this container.
         */
        public function __construct ($items = array (), $args = array ()) {
            parent::__construct ($items, $args);
        } // __construct ()
        
        
        
        
        /**
         *  Get the values from the fields in this meta box.
         *
         *  @return array The values from the meta box.
         */
        public function getValues () {
            $values = array ();
            
            foreach ($this->_items as $panel) {
                foreach ($panel->getFields () as $field) {
                    $values [$field->name] = $field->getValue ();
                } // foreach ()
            } // forech ()
            
            return $values;
        } // getValues ()
        
        /**
         *  Set the values for this meta box form.
         *
         *  @param array The values to set for the fields.
         *
         *  @return Fuse\Forms\Container\MetaBox This meta box object.
         */
        public function setValues ($values) {
            if (is_array ($values)) {
                foreach ($this->_items as $panel) {
                    foreach ($panel->getFields () as $field) {
                        $value = '';
                        
                        if (array_key_exists ($field->name, $values)) {
                            $value = $values [$field->name];
                        } // if ()
                        
                        $field->setValue ($value, $values);
                    } // foreach ()
                } // forech ()
            } // if ()
            
            return $this;
        } // setValues ()
        
        /**
         *  Set the fields values from the given posts meta values.
         *
         *  @param int $post_id The ID of the post the get the values for.
         *
         *  @return Fuse\Forms\Container\MetabBox This metabox object.
         */
        public function setValuesFromMeta ($post_id) {
            $values = array ();
            
            foreach (get_post_meta ($post_id) as $key => $value) {
                if (substr ($key, 0, 10) == 'fuse_form_') {
                    $field_id = substr ($key, 10);
                    
                    if (is_array ($value)) {
                        $value = $value [0];
                    } // if ()
                    
                    $values [$field_id] = $value;
                } // if ()
            } // foreach ()
            
            $this->setValues ($values);
            
            return $this;
        } // setValuesFromMeta ()
        
        
        
        
        /**
         *  Get the HTML code for this container.
         */
        public function render ($output = false) {
            ob_start ();
            ?>
                <div class="fuse-forms-metabox-container">
                    <?php
                        parent::render (true);
                    ?>
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
        
    } // class MetaBox