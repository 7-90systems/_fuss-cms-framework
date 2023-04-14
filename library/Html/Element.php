<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our base HTML element class.
     *
     *  @filter fuse_html_element_no_closing_tags
     */
    
    namespace Fuse\Html;
    
    
    class Element {
        
        /**
         *  @var string The tag for this element.
         */
        protected $_tag;
        
        /**
         *  @var array The attributes for this element.
         */
        protected $_attributes;
        
        /**
         *  @var mixed The content for this tag.
         */
        protected $_content;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $tag The tag for this element.
         *  @param array $attributes The attributes for the element.
         *  @param mixed $content The content for this element. This can be
         *  a standard echo-able value or an array of values. Content is only
         *  used for elements that have a closing tag.
         */
        public function __construct ($tag, $attributes = array (), $content = NULL) {
            $this->_tag = $tag;
            
            $this->setAttributes ($attributes, true);
            $this->setContent ($content);
        } // __construct ()
        
        
        
        
        /**
         *  Add an attribute for this element.
         *
         *  @param string $name The attribute name.
         *  @param mixed $value The value to add.
         *  @param bool $append True to append to the existing value or false to
         *  overwrite the existing value.
         *
         *  @return Fuse\Html\Element Returns this element.
         */
        public function addAttribute ($name, $value, $append = true) {
            $att_value = '';
            
            if ($append === true && array_key_exists ($name, $this->_attributes)) {
                $current = $this->_attributes [$name];
                
                if (is_array ($current)) {
                    if (is_array ($value)) {
                        $current = array_merge ($current, $value);
                    } // if ()
                    else {
                        $current [] = $value;
                    } // else
                    
                    $value = $current;
                } // if ()
                else {
                    if (is_array ($value)) {
                        $value = array_merge (array ($current), $value);
                    } // if ()
                    else {
                        $value = trim ($current.' '.$value);
                    } // else
                } // if ()
            } // if ()
            
            $this->_attributes [$name] = $value;
            
            return $this;
        } // addAttribute ()
        
        /**
         *  Set an attribute for this element. This will overwrite any existing
         *  value for that attribute.
         *
         *  @param string $name The attribute name.
         *  @param mixed $value The value to add.
         *
         *  @return Fuse\Html\Element Returns this element.
         */
        public function setAttribute ($name, $value) {
            $this->addAttribute ($name, $value, false);
            
            return $this;
        } // setAttibute ()
        
        /**
         *  Set attributes for this element. This will overwrite any existing
         *  attributes.
         *
         *  @param array $attributes An associative array of attribute values.
         *  @param bool $clear True to clear all existing values.
         *
         *  @return Fuse\Html\Element Returns this element.
         */
        public function setAttributes ($attributes, $clear = false) {
            if ($clear === true) {
                $this->_attributes = array ();
            } // if ()
            
            foreach ($attributes as $key => $value) {
                $this->setAttribute ($key, $value);
            } // foreach ()
            
            return $this;
        } // setAttributes ()
        
        /**
         *  Get the value set for the given attribute.
         *
         *  @param string $name The name of the attribute to get.
         *
         *  @return mixed Returns the attribute value or NULL if the value
         *  does not exist.
         */
        public function getAttribute ($name) {
            $value = NULL;
            
            if (array_key_exists ($name, $this->attributes)) {
                $value = $this->_attributes [$name];
            } // if ()
            
            return $value;
        } // getAttribute ()
        
        /**
         *  Remove at attribute.
         *
         *  @param string $name The name of the attribute to remove.
         *
         *  @return Fuse\Html\Element Returns this element.
         */
        public function removeAttribute ($name) {
            unset ($this->_attributes [$name]);
            
            return $this;
        } // removeAttribute ()
        
        
        
        
        /**
         *  Get the content for this element.
         *
         *  @return mixed The content for this element.
         */
        public function getContent () {
            return $this->_content;
        } // getContent ()
        
        /**
         *  Add content for this element.
         *
         *  @param mixed $content The content to add.
         *  @param bool $append True to append or false to overwrite.
         *
         *  @return Fuse\Html\Element This element.
         */
        public function addContent ($content, $append = true) {
            if ($append === false) {
                $this->_content = NULL;    
            } // if ()
            
            if (empty ($content) === false) {
                if (is_array ($this->_content)) {
                    if (is_array ($content)) {
                        $this->_content = array_filter (array_merge ($this->_content, $content));
                    } // if ()
                    elseif (empty ($content) === false) {
                        $this->_content = array_filter (array_merge ($this->_content, array ($content)));
                    } // elseif ()
                    else {
                        $this->_content = $content;
                    } // else
                } // if ()
                elseif (empty ($content) === false) {
                    if (is_array ($content)) {
                        $this->_content = array_filter (array_merge (array ($this->_content), $content));
                    } // if ()
                    else {
                        $this->_content = trim ($this->_content.' '.$content);
                    } // else
                } // else
            } // if ()
            
            return $this;
        } // addContent ()
        
        /**
         *  Set the content for this element. This will over-write any existing
         *  content.
         *
         *  @param mixed $content The content to add.
         *
         *  @return Fuse\Html\Element This element.
         */
        public function setContent ($content) {
            $this->addContent ($content, false);
            
            return $this;
        } // setContent ()
        
        
        
        
        /**
         *  Does this element have a closing tag?
         *
         *  @return bool True if this element needs a closing tag or false if
         *  it does not need a closing tag.
         */
        public function hasClosingTag () {
            $has_closing_tag = true;
            
            $no_close_tags = apply_filters ('fuse_html_element_no_closing_tags', array (
                'area',
                'base',
                'br',
                'col',
                'command',
                'hr',
                'img',
                'input',
                'keygen',
                'link',
                'meta',
                'param',
                'source',
                'track',
                'wbr'
            ));
            
            if (in_array ($this->_tag, $no_close_tags)) {
                $has_closing_tag = false;
            } // if ()
            
            return $has_closing_tag;
        } // hasClosingTag ()
        
        
        
        
        /**
         *  Render this element!
         *
         *  @param bool $output True to output the HTML code or false to return
         *  it. Defaults to false.
         */
        public function render ($output = false) {
            $html = '<'.$this->_tag.fuse_format_attributes ($this->_attributes, true, false);
            
            $content = $this->getContent ();
            
            if ($this->hasClosingTag () === false) {
                // We use a trailing slash here for HTML 4/XHTML compatibility.
                $html.= ' />';
            } // if ()
            else {
                $html.= '>';
                
                if (empty ($content) === false) {
                    if (is_array ($content)) {
                        foreach ($content as $item) {
                            $html.= $item.'';
                        } // foreach ()
                    } // if ()
                    else {
                        $html.= $content.'';
                    } // else
                } // if ()
                
                $html.= '</'.$this->_tag.'>';
            } // else
            
            if ($output === true) {
                echo $html;
            } // if ()
            else {
                return $html;
            } // else
        } // render ()
        
        
        
        
        /**
         *  Output the HTML code.
         */
        public function __toString () {
            return $this->render (false);
        } // __toString ()
        
    } // class Element