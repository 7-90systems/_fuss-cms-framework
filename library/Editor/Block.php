<?php
    /**
     *  @package fusecms
     *
     *  This class is the base for any user-defined Gutenberg blocks.
     *
     *  @filter fuse_block_*BLOCKID*_depenencies_filter Filters the block
     *  dependencies for the block ID.  The block ID has / converted to _ for
     *  the system.
     */
    
    namespace Fuse\Editor;
    
    
    class Block {
        
        /**
         *  @var string the block ID.
         */
        protected $_block_id;
        
        /**
         *  @var string The URL of the editor script.
         */
        protected $_editor_script;
        
        /**
         *  @var string The URL of the public CSS stylesheet.
         */
        protected $_style;
        
        /**
         *  @var string The URL of the editor CSS stylesheet.
         */
        protected $editor_style;
        
        /**
         *  @var mixed The callback function for this script.
         */
        protected $_render_callback;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $block_id The ID of this block. This is in the form of
         *  'namespace/block-name'
         *  @param string $editor_url The full URL of the JavaScript definition
         *  file for this block.
         *  @param array $args The additional arguments for this block. Values
         *  are:
         *      style               - The full URL of the public-facing CSS stylesheet.
         *      editor_stype        - The full URL of the editor CSS stylesheet.
         *      render_callback     - The callback function to be used by the block.
         */
        public function __construct ($block_id, $editor_script, $args = array ()) {
            $this->_block_id = $block_id;
            $this->_editor_script = $editor_script;
            
            if (array_key_exists ('style', $args) && empty ($args ['style']) === false) {
                $this->_style = $args ['style'];
            } // if ()
            
            if (array_key_exists ('editor_style', $args) && empty ($args ['edtor_style']) === false) {
                $this->_editor_style = $args ['editor_style'];
            } // if ()
            
            if (array_key_exists ('render_callback', $args) && empty ($args ['render_callback']) === false) {
                $this->_render_callback = $args ['render_callback'];
            } // if ()
            
            add_action ('init', array ($this, 'registerBlock'));
        } // __construct ()
        
        
        
        
        /**
         *  This function registers the block with the editor system.
         */
        public function registerBlock () {
            $script_id = $this->_block_id.'-script';
            $deps = apply_filters ('fuse_block_'.str_replace ('/', '_', $this->_block_id).'_depenencies_filter', array ('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'));
            
            wp_register_script ($script_id, $this->_editor_script, $deps);
            
            $register_args = array (
                'editor_script' => $script_id
            );
            
            // Check for public stylesheet
            if (empty ($this->_style) === false) {
                $style_id = str_replace ('/', '-', $this->_block_id).'-style';
                
                wp_register_style ($style_id, $this->_style, array ('wp-edit-blocks'));
                
                $register_args ['style'] = $style_id;
            } // if ()
            
            // Check for editor stylesheet
            if (empty ($this->_editor_style) === false) {
                $style_id = str_replace ('/', '-', $this->_block_id).'-editor';
                
                wp_register_style ($style_id, $this->_style, array ('wp-edit-blocks'));
                
                $register_args ['editor_style'] = $style_id;
            } // if ()
            
            // Check for render callback function
            if (empty ($this->_render_callback) === false) {
                $register_args ['render_callack'] = array ($this, $this->_render_callback);
            } // if ()
            
            register_block_type ($this->_block_id, $register_args);
        } // registerBlock ()
        
    } // class Block