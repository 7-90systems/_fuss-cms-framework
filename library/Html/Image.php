<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This class is used to set up a HTML image tag. This can be used as a
     *  standard <img> tag or as a responsive image by adding additional
     *  source objects.
     */
    
    namespace Fuse\Html;
    
    use Fuse\Html\Image\Source;
    
    
    class Image {
        
        /**
         *  @var string The image URL.
         */
        protected $_image_url;
        
        /**
         *  @var string The images alt text.
         */
        protected $_alt_text;
        
        /**
         *  @var int The image width in pixels.
         */
        protected $_width;
        
        /**
         *  @var int The image height in pixels.
         */
        protected $_height;
        
        /**
         *  @var array The image attributes.
         */
        protected $_attributes;
        
        /**
         *  @var array The sources used to build a srcset meta value.
         */
        protected $_srcset;
        
        
        
        
        /**
         *  Object constructor.
         */
        public function __construct (string $source_url, string $alt_text = '', int $width, int $height, array $attributes = array (), array $sources = array ()) {
            $this->setSourceUrl ($source_url);
            $this->setAltText ($alt_text);
            $this->setWidth ($width);
            $this->setHeight ($height);
            $this->setAttributes ($attributes);
            $this->setSourceSets ($sources);
        } // __construct ()
        
        
        
        
        /**
         *  Set the images main source URL.
         *
         *  @param string $source_url The source URL for this image. This should
         *  normally be a fully qualified URL, but may be relative if needed.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setSourceUrl (string $source_url) {
            $this->_image_url = $source_url;
            
            return $this;
        } // setSourceUrl ()
        
        /**
         *  Set the alt text for this image.
         *
         *  @param string The images alt text.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setAltText (string $alt_text) {
            $this->_alt_text = $alt_text;
            
            return $this;
        } // setAltText ()
        
        /**
         *  Set the width for this image. Be aware that this is for the main
         *  image only, and width may vary for any additional source images.
         *
         *  @param int $width The images width in pixels.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setWidth (int $width) {
            $this->_width = $width;
            
            return $this;
        } // setWidth ()
        
        /**
         *  Set the height for this image. Be aware that this is for the main
         *  image only, and height may vary for any additional source images.
         *
         *  @param int $height The images height in pixels.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setHeight (int $height) {
            $this->_height = $height;
            
            return $this;
        } // setHeight ()
        
        
        
        
        /**
         *  Set an attribute.
         *
         *  @param string $name The name of the attribute to set. Note that you
         *  can't set width, height or alt attributes. These must be set using
         *  the available class methods.
         *  @param string $value The value for the attribute.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setAttribute (string $name, $value) {
            $not_allowed = array (
                'width',
                'height',
                'alt'
            );
            
            if (in_array ($name, $not_allowed) === false) {
                $this->_attributes [$name] = $value;
            } // if ()
            
            return $this;
        } // setAttribute ()
        
        /**
         *  Set the attributes for this image. This will clear any existing
         *  attribute values.
         *
         *  @param array $attributes The attributes to set.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setAttributes (array $attributes) {
            $this->_attributes = array ();
            
            foreach ($attributes as $name => $value) {
                $this->setAttribute ($name, $value);
            } /// foreach ()
            
            return $this;
        } // setAttributes ()
        
        /**
         *  Remove an attribute.
         *
         *  @param string $name The name of the attribute to remove.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function clearAttribute (string $name) {
            if (array_key_exists ($name, $this>_attributes)) {
                unset ($this->_attributes [$name]);
            } // if ()
            
            return $this;
        } // clearAttribute ()
        
        
        
        
        /**
         *  Add a source value for this image.
         *
         *  @param Fuse\Html\Image\Source $source The image source.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function addSource (Source $source) {
            $this->_srcset [] = $source;
            
            return $this;
        } // addSource ()
        
        /**
         *  Set the source set images.
         *
         *  @param array $sources The source images.
         *
         *  @return Fuse\Html\Image This image object.
         */
        public function setSourceSets (array $sources) {
            $this->_srcset = array ();
            
            foreach ($sources as $source) {
                $this->addSource ($source);
            } // foreach ()
            
            return $this;
        } // setSourceSets ()
        
        
        
        
        /**
         *  Render this image!
         *
         *  @param bool $output True to output or fasle to return.
         *
         *  @return string The images HTML code.
         */
        public function render (bool $output = true) {
            $attributes = array (
                'src' => $this->_image_url,
                'alt' => $this->_alt_text,
                'width' => $this->_width,
                'height' => $this->_height
            );
            
            if (count ($this->_srcset) > 0) {
                $srcset = array (
                    $this->_image_url.' '.$this->_width.'w'
                );
                $sizes = array ();
                
                foreach ($this->_srcset as $source) {
                    $srcset [] = $source->getSrcSet ();
                    $sizes [] = $source->getSize ();
                } // foreach ()
                
                $sizes [] = $this->_width.'px';
                
                $attributes ['srcset'] = implode (', ', $srcset);
                $attributes ['sizes'] = implode (', ', $sizes);
            } // if ()
            
            $html = '<img '.fuse_format_attributes (array_merge ($attributes, $this->_attributes), false, false).' />';
            
            if ($output === true) {
                echo $html;
            } // if ()
            else {
                return $html;
            } //else
        } // render ()
        
    } // class Image