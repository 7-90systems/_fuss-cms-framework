<?php
    /**
     *  @package fusecms
     *
     *  Set up our content_block shortcode.
     */
    
    namespace Fuse\Shortcode;
    
    use Fuse\Shortcode;
    
    
    class ContentBlock extends Shortcode {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('content_block', 'content-block', array (
                'id' => '',
                'class' => ''
            ));
        } // __construct ()
        
    } // class ContentBlock