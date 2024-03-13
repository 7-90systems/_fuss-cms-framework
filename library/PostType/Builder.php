<?php
    /**
     *  @package fusecms
     *
     *  This is our post tyep builder class. We use this to allow non-technical
     *  users to create new post types quickly and easily in the admin area.
     */
    
    namespace Fuse\PostType;
    
    use Fuse\PostType;
    
    
    class Builder extends PostType {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('fuse_posttype', __ ('Post Type', 'fuse'), __ ('Post Type Builder'), array (
                'public' => false,
                'publicly_queryable' => false,
                'rewrite' => false,
                'show_in_rest' => false,
                'show_in_menu' => 'tools.php',
                'supports' => array (
                    'title'
                )
            ));
        } // __construct ()
        
    } // class Builder