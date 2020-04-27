<?php
    /**
     *  @package fusecms
     *
     *  Set up our content_column shortcode.
     */
    
    namespace Fuse\Shortcode;
    
    use Fuse\Shortcode;
    
    
    class ContentColumn extends Shortcode {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('content_column', 'content-column', array (
                'id' => '',
                'class' => '',
                'position' => 'left',           // 'left', 'right'
                'size' => 'half',               // 'half', 'third', 'twothirds', quarter'
                'clear' => ''                   // '' (default), 'clear', 'none'
            ));
        } // __construct ()
        
    } // class ContentColumn