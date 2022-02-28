<?php
    /**
     *  @package fusecms
     *
     *  This is our base post type class. All post types should inherit from
     *  this class.
     */
    
    namespace Fuse;
    
    
    class PostType {
        
        /**
         *  @var string This is the slug for this post type.
         */
        private $_slug;
        
        /**
         *  @param string This is the singular name for this post type.
         */
        private $_name_singular;
        
        /**
         *  @param string This is the plural name for this post type.
         */
        private $_name_plural;
        
        /**
         *  @var array The arguments for this post type.
         */
        private $_args;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $slug The post type slug for this post type.
         *  @param string $name_singular The singular name for this post type.
         *  @param string $plural The plural name for this post type.
         *  @param array $args The arguments for this post type.
         */
        public function __construct ($slug, $name_singular, $name_plural = '', $args = array ()) {
            if (strlen ($name_plural) == 0) {
                $name_plural = apply_filters ('fuse_posttype_plural_name', sprintf (__ ('%ss', 'fuse'), $name_singular), $name_singular);
            } // if ()
            
            $this->_slug = $slug;
            $this->_name_singular = $name_singular;
            $this->_name_plural = $name_plural;
            $this->_args = $args;
            
            // Set up the post type registration.
            add_action ('init', array ($this, 'registerPostType'));
            add_action ('init', array ($this, 'registerTaxonomies'));
            
            /**
             *  TODO: Check out the details at https://rudrastyh.com/gutenberg/plugin-sidebars.html
             */
            add_action ('add_meta_boxes', array ($this, 'addMetaBoxes'), 9);
            add_action ('save_post_'.$slug, array ($this, 'savePostValues'), 10, 3);
            
            // Set up our admin list columns
            add_filter ('manage_edit-'.$slug.'_columns', array ($this, 'adminListColumns'));
            add_filter ('manage_'.$slug.'_posts_custom_column', array ($this, 'adminListValues'), 10, 2);
            add_filter ('manage_'.$slug.'_pages_custom_column', array ($this, 'adminListValues'), 10, 2);
        } // __construct ()
        
        
        
        
        /**
         *  Register this post type.
         */
        final public function registerPostType () {
            $labels = array (
                'name' => $this->_name_plural,
                'singular_name' => $this->_name_singular,
                'add_new' => __ ('Add New', 'fuse'),
                'add_new_item' => sprintf (__ ('Add New %s', 'fuse'), $this->_name_singular),
                'edit_item' => sprintf (__ ('Edit %s', 'fuse'), $this->_name_singular),
                'new_item' => sprintf (__ ('New %s', 'fuse'), $this->_name_singular),
                'view_item' => sprintf (__ ('View %s', 'fuse'), $this->_name_singular),
                'search_items' => sprintf (__ ('Search %s', 'fuse'), $this->_name_plural),
                'not_found' =>  __ ('Nothing found', 'fuse'),
                'not_found_in_trash' => __ ('Nothing found in Trash', 'fuse'),
                'parent_item_colon' => ''
            );
        
            $args = array (
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'show_ui' => true,
                'query_var' => true,
                'menu_icon' => FUSE_BASE_URL.'/assets/images/icons/fuse_16.png',
                'rewrite' => true,
                'capability_type' => 'post',
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array (
                    'title',
                    'editor',
                    'thumbnail'
                )
            );
            
            $args = array_merge ($args, $this->_args);
            
            register_post_type ($this->_slug, $args);
        } // registerPostType ()
        
        /**
         *  Register our taxonomies.
         */
        public function registerTaxonomies () {
            /**
             *  Over-ride this function as required.
             */
        } // registerTaxonomies ()
        
        
        
        
        /**
         *  Check if we should save the posts values.
         */
        final public function savePostValues ($post_id, $post) {
            if (defined ('DOING_AUTOSAVE') === false || DOING_AUTOSAVE !== true) {
                $this->savePost ($post_id, $post);
            } // if ()
        } // savePostValues ()
        
        
        
        
        /**
         *  Set up to add meta boxes.
         *
         *  Use the add_meta_box () function to set up the meta boxes that you need for your post type.
         */
        public function addMetaBoxes () {
            /**
             *  Over-ride this function as required.
             */
        } // addMetaBoxes ()
        
        
        
        
        /**
         *  Register our meta values when using the Gutenberg editor.
         *
         *  Override this function and use the register_meta() function to set up the meta values
         *  that you need for your post types.
         */
        public function registerMeta () {
            /**
             *  Over-ride this function as required.
             */
        } // registerMeta ()
        
        
        
        
        /**
         *  Save the posts values.
         *
         *  @param int $post_id The ID of the post being updated.
         *  @param WP_Post $post The post being updated
         *  @param bool $update True if this is an existing post.
         */
        public function savePost ($post_id, $post) {
            /**
             *  Over-ride this function as required.
             */
        } // savePost ()
        
        
        
        
        /**
         *  Allow others to over-ride the existing admin list columns.
         *
         *  @param array $columns The existing columns.
         *
         *  @return array The completed column list
         */
        public function adminListColumns ($columns) {
            return $columns;
        } // adminListColumns ()
        
        /**
         *  Output the values for our custom admin list columns.
         *
         *  @param string $column The name of the column
         *  @param int $post_id The ID of the post.
         */
        public function adminListValues ($column, $post_id) {
            // Don't do anything here, but you can do it yourself!
        } // adminListValues ()
        
        
        
        
        /**
         *  Get the slug for this post type.
         *
         *  @return string The slug for this post type.
         */
        final public function getSlug () {
            return $this->_slug;
        } // getSlug ()
        
    } // class PostType