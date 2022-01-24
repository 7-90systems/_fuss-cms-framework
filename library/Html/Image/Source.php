<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This class represents an image used as part of an image tags srcset
     *  definition.
     */
    
    namespace Fuse\Html\Image;
    
    
    class Source {
        
        /**
         *  @var string The image URL.
         */
        public $image_url;
        
        /**
         *  @var int The image width.
         */
        protected $_width;
        
        /**
         *  @var string The media condition for this source image.
         */
        protected $_media_condition;
        
        /**
         *  @var int $slot_width The width of the "slot" that the image will
         *  fill for the given media condition.
         */
        protected $_slot_width;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $image_url The URL of the image.
         *  @param int $intrinsic_width The width of the image in pixels.
         *  @param string $media_condition The media condition for displaying
         *  this image, eg: 'max-width: 1000px'. This does not need to be
         *  inside brackets.
         */
        public function __construct (string $image_url, int $intrinsic_width, string $media_condition, int $slot_width = NULL) {
            if (empty ($slot_width)) {
                $slot_width = $intrinsic_width;
            } // if ()
            
            $this->image_url = $image_url;
            $this->_width = $intrinsic_width;
            $this->_media_condition = $media_condition;
            $this->_slot_width = $slot_width;
        } // __construct ()
        
        
        
        
        /**
         *  Get the srcset value for this image source.
         *
         *  return string The srcset value.
         */
        public function getSrcSet () {
            return esc_attr ($this->image_url.' '.$this->_width.'w');
        } // getSrcSet ()
        
        /**
         *  Get the size value for this image source.
         *
         *  @return string The size value.
         */
        public function getSize () {
            return esc_attr ('('.$this->_media_condition.') '.$this->_slot_width.'px');
        } // getSize ()
        
    } // class Source