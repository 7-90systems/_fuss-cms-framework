<?php
    /**
     *  @package fusecms
     *
     *  This class is used to install the various settings and posts for the
     *  Fuse CMS Frameowork.
     */
    
    namespace Fuse;
    
    
    class Install {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            // Set up our initial page layout.
            $this->_setupLayout ();
        } // __construct ()
        
        
        
        
        /**
         *  Set up our initial page layout.
         *
         *  We only set up an initial layout if no layouts exist.
         */
        protected function _setupLayout () {
            $layouts = get_posts (array (
                'numberposts' => 1,
                'post_type' => 'fuse_layouts'
            ));
            
            if (count ($layouts) == 0) {
                $layout_id = wp_insert_post (array (
                    'post_title' => __ ('Global Default Layout', 'fuse'),
                    'post_type' => 'fuse_layouts',
                    'post_status' => 'publish'
                ));
                
                if ($layout_id > 0) {
                    // Created successfully
                    add_post_meta ($layout_id, 'fuse_layout_parts', array (
                        'header' => true,
                        'left_1' => false,
                        'left_2' => false,
                        'right_1' => true,
                        'right_2' => false,
                        'footer' => true
                    ));
                    
                    add_post_meta ($layout_id, 'fuse_parts_sidebar_left_1', 'default');
                    add_post_meta ($layout_id, 'fuse_parts_sidebar_left_2', 'default');
                    add_post_meta ($layout_id, 'fuse_parts_sidebar_right_1', 'default');
                    add_post_meta ($layout_id, 'fuse_parts_sidebar_right_2', 'default');
                    
                    add_option ('fuse_layout_defaults_global', $layout_id);
                } // if ()
            } // if ()
        } // _setupLayout ()
        
    } // class Install