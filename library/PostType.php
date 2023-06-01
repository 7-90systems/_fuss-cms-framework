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
         *  @var string This is the parent post type for this post type.
         *  Setting the parent post type in your class will set up the
         *  required functionality to link this post type to the parent.
         */
        protected $_parent_post_type = '';
        
        /**
         *  @var bool This value tells us if we should register the post type
         *  or not. This is useful if the post type already exists but we
         *  want to add some functionality to it with our own class.
         */
        protected $_register_post_type = true;
        
        
        
        
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
                $name_plural = apply_filters ('fuse_posttype_name_plural', sprintf (__ ('%ss', 'fuse'), $name_singular), $name_singular);
            } // if ()
            
            $this->_slug = $slug;
            $this->_name_singular = $name_singular;
            $this->_name_plural = $name_plural;
            $this->_args = $args;
            
            // Set up the post type registration.
            add_action ('init', array ($this, 'registerPostType'));
            add_action ('init', array ($this, 'registerTaxonomies'));
            
            add_action ('add_meta_boxes_'.$slug, array ($this, 'addMetaBoxes'), 9);
            add_action ('save_post_'.$slug, array ($this, 'savePostValues'), 10, 2);
            
            // Set up our admin list columns
            add_filter ('manage_edit-'.$slug.'_columns', array ($this, 'adminListColumns'));
            add_filter ('manage_'.$slug.'_posts_custom_column', array ($this, 'adminListValues'), 10, 2);
            add_filter ('manage_'.$slug.'_pages_custom_column', array ($this, 'adminListValues'), 10, 2);
            
            // Is this a child of another parent post type?
            if (strlen($this->_parent_post_type) > 0) {
                add_action ('add_meta_boxes_'.$slug, array ($this, 'addParentMetaBox'));
                add_action ('save_post_'.$slug, array ($this, 'saveParentPostType'), 10, 2);
            } // if ()
        } // __construct ()
        
        
        
        
        /**
         *  Register this post type.
         */
        final public function registerPostType () {
            // Register if allowed
            if ($this->_register_post_type !== false) {
                $labels = array (
                    'name' => $this->_name_plural,
                    'singular_name' => $this->_name_singular,
                    'add_new_item' => sprintf (__ ('Add New %s', 'fuse'), $this->_name_singular),
                    'edit_item' => sprintf (__ ('Edit %s', 'fuse'), $this->_name_singular),
                    'new_item' => sprintf (__ ('New %s', 'fuse'), $this->_name_singular),
                    'view_item' => sprintf (__ ('View %s', 'fuse'), $this->_name_singular),
                    'view_items' => sprintf (__ ('View %s', 'fuse'), $this->_name_plural),
                    'search_items' => sprintf (__ ('Search %s', 'fuse'), $this->_name_plural),
                    'not_found' => sprintf (__ ('No %s found', 'fuse'), strtolower ($this->_name_plural)),
                    'not_found_in_trash' => sprintf (__ ('No %s found in trash', 'fuse'), strtolower ($this->_name_plural)),
                    'parent_item_colon' => sprintf (__ ('Parent %s', 'fuse'), $this->_name_singular),
                    'all_items' => sprintf (__ ('All %s', 'fuse'), $this->_name_plural),
                    'archives' => sprintf (__ ('%s Archives', 'fuse'), $this->_name_singular),
                    'attributes' => sprintf (__ ('%s Attributes', 'fuse'), $this->_name_singular),
                    'insert_into_item' => sprintf (__ ('Insert into %s', 'fuse'), strtolower ($this->_name_singular)),
                    'uploaded_to_this_item' => sprintf (__ ('Uploaded to this %s', 'fuse'), strtolower ($this->_name_singular)),
                    'filter_items_list' => sprintf (__ ('Filter %s list', 'fuse'), strtolower ($this->_name_plural)),
                    'items_list_navigation' => sprintf (__ ('%s list navigation', 'fuse'), $this->_name_plural),
                    'items_list' => sprintf (__ ('%s list','fuse'), $this->_name_plural),
                    'item_published' => sprintf (__ ('%s published.', 'fuse'), $this->_name_singular),
                    'item_published_privately' => sprintf (__ ('%s published privately.', 'fuse'), $this->_name_singular),
                    'item_reverted_to_draft' => sprintf (__ ('%s reverted to draft.', 'fuse'), $this->_name_singular),
                    'item_scheduled' => sprintf (__ ('%s scheduled.', 'fuse'), $this->_name_singular),
                    'item_updated' => sprintf (__ ('%s updated.', 'fuse'), $this->_name_singular),
                    'item_link' => sprintf (__ ('%s Link', 'fuse'), $this->_name_singular),
                    'item_link_description' => sprintf (__ ('A link to a %s.', 'fuse'), strtolower ($this->_name_singular))
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
                
                // Is this a child post type?
                if (is_string ($this->_parent_post_type) && strlen ($this->_parent_post_type) > 0) {
                    $args ['show_in_menu'] = 'edit.php?post_type='.$this->_parent_post_type;
                } // if ()
                
                $args = array_merge ($args, $this->_args);
                
                register_post_type ($this->_slug, $args);
            } // if ()
        } // registerPostType ()
        
        
        
        
        /**
         *  Check if we should save the posts values.
         *
         *  @param int $post_id The ID of the post object.
         *  @param WP_Post $post The post object.
         */
        final public function savePostValues ($post_id, $post) {
            if (defined ('DOING_AUTOSAVE') === false || DOING_AUTOSAVE !== true) {
                $this->savePost ($post_id, $post);
            } // if ()
        } // savePostValues ()
        
        
        
        
        /**
         *  Add our parent post type meta box.
         */
        final public function addParentMetaBox () {
            add_meta_box ('fuse_posttype_parent_meta', __ ('Set Parent', 'fuse'), array ($this, 'parentMeta'), $this->getSlug (), 'side', 'high');
        } // addParentMetaBox ()
        
        /**
         *  Set up our parent post type meta box.
         *
         *  @param WP_Post $post The post object.
         */
        final public function parentMeta ($post) {
            ?>
                <select name="fuse_posttype_parent">
                    <option value="">&nbsp;</option>
                    <?php foreach ($this->_getParentPosts () as $row): ?>
                        <option value="<?php echo $row->ID; ?>"<?php selected ($row->ID, $post->post_parent); ?>><?php echo $row->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php
        } // parentMeta ()
        
        /**
         *  Save the parent ID for this post object.
         *
         *  @param int $post_id The ID of the post object.
         *  @param WP_Post $post The post object.
         */
        final public function saveParentPostType ($post_id, $post) {
            if (defined ('DOING_AUTOSAVE') === false || DOING_AUTOSAVE !== true) {
                if (array_key_exists ('fuse_posttype_parent', $_POST)) {
                    global $wpdb;
                    
                    $wpdb->update ($wpdb->posts, array (
                        'post_parent' => $_POST ['fuse_posttype_parent']
                    ), array (
                        'ID' => $post_id
                    ), array (
                        '%d'
                    ), array (
                        '%d'
                    ));
                } // if ()
            } // if ()
        } // saveParentPostType ()
        
        
        
        
        /**
         *  Get the slug for this post type.
         *
         *  @return string The slug for this post type.
         */
        final public function getSlug () {
            return $this->_slug;
        } // getSlug ()
        
        
        
        
        /**
         *  Get a list of posts form the parent post type.
         *
         *  @return array The post items. Each item has ID and post_title.
         */
        final private function _getParentPosts () {
            global $wpdb;
            
            $query = $wpdb->prepare ("SELECT
                ID,
                post_title
            FROM ".$wpdb->posts."
            WHERE post_type = %s
                AND post_status NOT IN('trash','inherit')
            ORDER BY post_title ASC", $this->_parent_post_type);
            
            return $wpdb->get_results ($query);
        } // _getParentPosts ()
        
        
        
        
        /**
         *  Register our taxonomies.
         */
        public function registerTaxonomies () {
            /**
             *  Over-ride this function as required.
             */
        } // registerTaxonomies ()
        
        
        
        
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
        
    } // class PostType