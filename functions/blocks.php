<?php
    /**
     *  @package fusecms
     *
     *  This file contains our block functions.
     *
     *  @filter fuse_block_default_fields The default block fields.
     *  @filter fuse_block_field_value Filter the fields value.
     *  @filter fuse_block_sub_field_value Filter the sub-field value.
     */
    
    
    
    
    /**
     *  Return the value of the requested field.
     *
     *  @param string $field_name The name of the field.
     *  @param bool $echo True to echo the value or false to return the value.
     */
    function fuse_block_field_value ($name, $echo = true) {
        $value = false;
        
        $attributes = $config = false;
        
        if (array_key_exists ('attributes', \Fuse\Block\Util::$data)) {
            $attributes = \Fuse\Block\Util::$data ['attributes'];
        } // if ()
        
        if (array_key_exists ('config', \Fuse\Block\Util::$data)) {
            $config = \Fuse\Block\Util::$data ['config'];
        } // if ()
        
        if (is_array ($attributes) && is_object ($config)) {
            $field = null;
            $control = null;
            
            if (array_key_exists ($name, $attributes)) {
                $value = $attributes [$name];
            } // if ()
            
            if (isset ($config->fields [$name])) {
                // Cast the value with the correct type.
                $field = $config->fields [$name];
                $value = $field->castValue ($value);
                $control = $field->control;
            } // if ()
            
            /**
             * Filters the value to be made available or echoed on the front-end
             * template.
             */
            $value = apply_filters ('fuse_block_field_value', $value, $control, $echo);
            
            if ($echo) {
                if ($field) {
                    $value = $field->castValueToString ($value);
                } // if ()
            
                /*
                 * Escaping this value may cause it to break in some use cases.
                 * If this happens, retrieve the field's value using block_value(),
                 * and then output the field with a more suitable escaping function.
                 */
                echo wp_kses_post ($value);
            } // if ()
        } // if ()
        
        return $value;
    } // fuse_block_field_value ()
    
    /**
     *  Return the value of a sub-field.
     *
     *  @param string $name The name of the sub-field.
     *
     *  @param bool $echo Whether to echo and return the field, or just return
     *  the field.
     *
     *  @return mixed
     */
    function fuse_block_sub_field_value ($repeater, $name, $echo = false, $index = false) {
        $value = false;
        
        $loop = \Fuse\Block\Util\Loop::getInstance ();
        
        $row = $loop->getRepeaterRowAt ($repeater, $index);
        
        if ($row !== false && array_key_exists ($name, $row)) {
            $value = $row [$name];
            $config = \Fuse\Block\Util::$data ['config'];
        
            if ($config) {
                if (isset ($config->fields [$repeater])) {
                    $control = NULL;
                
                        $row_attributes = $row [$name];
                    
                        if (array_key_exists ($name, $row)) {
                            $field = $config->fields [$repeater]->settings ['sub_fields'][$name];
                            $control = $field->control;
                            $value = $row [$name];
                            $value = $field->castValue ($value);
                        
                            /**
                             * Filters the value to be made available or echoed on the front-end template.
                             *
                             * @param mixed       $value The value.
                             * @param string|null $control The type of the control, like 'user', or null if this is the 'className', which has no control.
                             * @param bool        $echo Whether or not this value will be echoed.
                             */
                            $value = apply_filters ('fuse_block_sub_field_value', $value, $control, $echo);
                        
                            if ($echo) {
                                $value = $field->castValueToString ($value);
                        
                                /*
                                 * Escaping this value may cause it to break in some use cases.
                                 * If this happens, retrieve the field's value using block_value(),
                                 * and then output the field with a more suitable escaping function.
                                 */
                                echo wp_kses_post ($value);
                            } // if ()
                        } // if ()
                } // if ()
            } // if ()
        } // if ()
        
        return $value;
    } // fuse_block_sub_field_value ()
    
    
    
    
    /**
     *  Set up a repeater loop.
     */
    function fuse_block_repeater_loop ($repeater) {
        $loop = \Fuse\Block\Util\Loop::getInstance ();
        
        return $loop->setLoop ($repeater);
    } // fuse_block_repeater_loop ()
    
    /**
     *  Reset a repeater loop
     */
    function fuse_block_repeater_reset ($repeater) {
        $loop = \Fuse\Block\Util\Loop::getInstance ();
        
        $loop->resetLoop ($repeater);
    } // fuse_block_repeater_reset ()
    
    /**
     *  Increment the repeater row.
     */
    function fuse_block_repeater_increment ($repeater) {
        $loop = \Fuse\Block\Util\Loop::getInstance ();
        
        $loop->increment ($repeater);
    } // fuse_block_repeater_increment ()
    
    /**
     *  Prepare a loop with the first or next row in a repeater.
     *
     *  @param string $name The name of the repeater field.
     *
     *  @return 
     */
    function fuse_block_repeater_row ($repeater) {
        $loop = \Fuse\Block\Util\Loop::getInstance ();

        return $loop->getRepeaterRow ($repeater);
    } // fuse_block_row ()