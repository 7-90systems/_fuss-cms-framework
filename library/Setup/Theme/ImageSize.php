<?php
    /**
     *  @package fusecms
     *
     *  This class represents an image size.
     */
    
    namespace Fuse\Setup\Theme;
    
    
    class ImageSize {
        
        /**
         *  @var string The image size alias.
         */
        public $alias;
        
        /**
         *  @var string The image size width.
         */
        public $width;
        
        /**
         *  @var string The image size height.
         */
        public $height;
        
        /**
         *  @var string The image size crop mode.
         */
        public $crop;
        
        
        
        
        /**
         *  Object constructor.
         */
        public function __construct ($alias, $width, $height, $crop = false) {
            $this->alias = $alias;
            $this->width = intval ($width);
            $this->height = intval ($height);
            $this->crop = $crop;
        } // __construct ()
        
    } // class ImageSize