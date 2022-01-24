<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is the base for our Gutenberg patterns These are how we make up the
     *  various block templates for the site.
     */
    
    namespace Fuse\Block;
    
    
    class Pattern {
        
        /**
         *  @var string this is the Gutenberg "namespace" for our patterns. You
         *  can create your own namespace by extending this class and changing
         *  this value.
         */
        protected $_namespace = 'fuse-cms';
        
        
        
        
        /**
         *  @var string The pattern ID. This is used with the namespace to
         *  create the blocks individual ID in the Gutenberg system. If you
         *  create two patterns with the same ID one will be over-written.
         */
        protected $_pattern_id;
        
        /**
         *  @var string The patterns title.
         */
        protected $_title;
        
        /**
         *  @var string The description of this pattern.
         */
        protected $_description;
        
        /**
         *  @var string The content for this pattern.
         */
        protected $_content;
        
        /**
         *  @var array This array holds the categories that the pattern will be
         *  in.
         */
        protected $_categories;
        
        
        
        
        /**
         *  Object constructor.
         */
        public function __construct ($pattern_id, $title, $description, $content = '', $categories = array ()) {
            $this->_pattern_id = $pattern_id;
            $this->_title = $title;
            $this->_description = $description;
            $this->_content = $content;
            $this->_categories = $categories;
        } // __construct ()
        
        
        
        
        /**
         *  REgister our gubenberg block pattern.
         */
        final public function registerPattern () {
            register_block_pattern ($this->_namespace.'/'.$this->_pattern_id, array (
                'title' => $this->_title,
                'description' => $this->_description,
                'content' => $this->_content,
                'categories' => $this->_categories
            ));
        } // registerPattern ()
        
    } // class Pattern