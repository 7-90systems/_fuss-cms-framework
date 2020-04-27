<?php
    /**
     *  @package fusecms
     *
     *  This is our block post type.
     *
     *  @filter fuse_block_controls Filter the available controls.
     *  @filter fuse_blocks Filter the list of blocks to register.
     *  @filter fuse_get_block_attributes Filter block attributes.
     *  @filter fuse_block_override_theme_template Filter the theme template.
    */
        
    namespace Fuse\PostType;
        
    use Fuse\PostType;
    use Fuse\Block\Util;
    use Fuse\Block\Field;
    
    // use Block_Lab\Blocks\Controls;
        
        
        
    class Block extends PostType {
        
        /**
         *  @var array Registered controls.
         */
        public $controls = array ();
        
        /**
         *  @var array Registered blocks.
         */
        protected $_blocks = array ();
            
            
            
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('fuse_block', __ ('Block', 'fuse'), __ ('Blocks', 'fuse'), array (
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'query_var' => false,
                'show_in_menu' => 'themes.php',
                'rewrite' => false,
                'capabilities'  => $this->_getCapabilities (),
                'supports' => array (
                    'title'
                )
            ));
            
            /**
             *  Register our action and filter hooks.
             */
            add_action ('admin_init', array ($this, 'addCaps'));
            add_action ('admin_init', array ($this, 'rowExport'));
            
            add_filter ('enter_title_here', array ($this, 'postTitlePlaceholder'));
            
            add_action ('post_submitbox_misc_actions', array ($this, 'postTypeCondition'));
            add_action ('admin_enqueue_scripts', array ($this, 'enqueueScripts'));
            add_action ('wp_insert_post_data', array ($this, 'insertBlock'), 10, 2);
            add_action ('init', array ($this, 'registerControls'));
            
            add_filter ('fuse_block_field_value', array ($this, 'getFieldValue'), 10, 3);
            add_filter ('fuse_block_sub_field_value', array ($this, 'getFieldValue'), 10, 3);
            
            // Clean up the list table.
            add_filter ('disable_months_dropdown', '__return_true', 10, $this->getSlug ());
            add_filter ('page_row_actions', array ($this, 'pageRowActions'), 10, 1);
            add_filter ('bulk_actions-edit-'.$this->getSlug (), array ($this, 'bulkActions'));
            add_filter ('handle_bulk_actions-edit-'.$this->getSlug (), array ($this, 'bulkExport'), 10, 3);
        
            // AJAX Handlers.
            add_action ('wp_ajax_fetch_field_settings', array ($this, 'ajaxFieldSettings'));
            
            // Set up our block registration
            add_action ('init', array ($this, 'retrieveBlocks'), 1);
            add_action ('init', array ($this, 'dynamicBlockLoader'), 10);
            
            add_action ('enqueue_block_editor_assets', array ($this, 'editorAssets'));
            
            add_filter ('block_categories', array ($this, 'registerCategories'));
        } // __construct ()
        
        
        
        
        /**
         *  Add our custom capabilities.
         */
        public function addCaps () {
            $admin = get_role ('administrator');
            
            if ($admin) {
                foreach ($this->_getCapabilities () as $capability => $custom_capability) {
                    $admin->add_cap ($custom_capability);
                } // foreach ()
            } // if ()
        } // addCaps ()
        
        /**
         *  Load all the published blocks and blocks/block.json files.
         */
        public function retrieveBlocks () {
            /**
             *  Retrieve blocks from blocks.json.
             *  Reverse to preserve order of preference when using array_merge.
             */
            $blocks_files = array_reverse (Util::locateTemplate ('blocks/blocks.json', '', false));
            
            foreach ($blocks_files as $blocks_file) {
                // This is expected to be on the local filesystem, so file_get_contents() is ok to use here.
                $json = file_get_contents ($blocks_file);
                $block_data = json_decode ($json, true);
    
                // Merge if no json_decode error occurred.
                if (json_last_error () == JSON_ERROR_NONE) {
                    $this->_blocks = array_merge ($this->_blocks, $block_data);
                } // if ()
            } // foreach ()
    
            /**
             * Retrieve blocks stored as posts in the WordPress database.
             */
            $block_posts = new \WP_Query (array (
                'post_type' => 'fuse_block',
                'post_status' => 'publish',
                'posts_per_page' => 100, // This has to have a limit for this plugin to be scalable.
            ));
    
            if ($block_posts->post_count > 0) {
                foreach ($block_posts->posts as $post) {
                    $block_data = json_decode ($post->post_content, true);
    
                    // Merge if no json_decode error occurred.
                    if (json_last_error () == JSON_ERROR_NONE) {
                        $this->_blocks = array_merge ($this->_blocks, $block_data);
                    } // if ()
                } // foreach ()
            } // if ()

            $this->_blocks = apply_filters ('fuse_blocks', $this->_blocks);
        } // retrieveBlocks ()
        
        /**
         *  Loads dynamic blocks via render_callback for each block.
         */
        public function dynamicBlockLoader () {
            if (function_exists ('register_block_type')) {
                foreach ($this->_blocks as $block_name => $block_config) {
                    $block = new \Fuse\Block ();
                    $block->fromArray ($block_config);
                    $this->registerBlock ($block_name, $block);
                } // foreach ()
            } // if ()
        } // dynamicBlockLoader ()
    
        /**
         *  Registers a block.
         *
         *  @param string $block_name The name of the block, including
         *  namespace.
         *  @param Block  $block The block to register.
         */
        public function registerBlock ($block_name, $block) {
            $attributes = $this->_getBlockAttributes ($block);
    
            // sanitize_title() allows underscores, but register_block_type doesn't.
            $block_name = str_replace ('_', '-', $block_name);
    
            // register_block_type doesn't allow slugs starting with a number.
            if (is_numeric ($block_name [0])) {
                $block_name = 'fuse-block-'.$block_name;
            } // if ()
    
            register_block_type ($block_name, array (
                'attributes' => $attributes,
                // @see https://github.com/WordPress/gutenberg/issues/4671
                'render_callback' => function ($attributes) use ($block) {
                    return $this->_renderBlockTemplate ($block, $attributes);
                }
            ));
        } // registerBlock ()
        
        /**
         *  Enqueue scripts and styles used by the Block post type.
         */
        public function enqueueScripts () {
            $post = get_post ();
            $screen = get_current_screen ();
        
            if (is_object ($screen)) {
                // if ($this->getSlug () === $screen->post_type && 'post' === $screen->base) {
                if ('post' === $screen->base) {
                    wp_enqueue_style ('fuse-block-post', FUSE_BASE_URL.'/assets/css/block/post.css');
        
                    if (!in_array ($post->post_status, array ('publish', 'future', 'pending'), true)) {
                        wp_add_inline_style ('fuse-block-post', '#delete-action {display: none;}');
                    } // if ()
        
                    wp_enqueue_script ('fuse-block-post', FUSE_BASE_URL.'/assets/javascript/block/post.js', array ('jquery', 'jquery-ui-sortable', 'wp-util', 'wp-blocks'));
        
                    $localed = wp_localize_script (
                        'fuse-block-post',
                        'blockLab',
                        array (
                            'fieldSettingsNonce' => wp_create_nonce ('block_lab_field_settings_nonce'),
                            'postTypes' => array (
                                'all'  => __ ('All', 'fuse'),
                                'none' => __ ('None', 'fuse')
                            ),
                            'copySuccessMessage' => __ ('Copied to clipboard.', 'fuse'),
                            'copyFailMessage' => sprintf (__ ('%1$s to copy.', 'fuse'), strpos (getenv ('HTTP_USER_AGENT'), 'Mac') ? 'Cmd+C' : 'Ctrl+C')
                        )
                    );
                } // if ()
        
                if ($this->getSlug () === $screen->post_type && 'edit' === $screen->base) {
                    wp_enqueue_style ('fuse-block-edit', FUSE_BASE_URL.'/assets/css/block/edit.css');
                } // if ()
            } // if ()
        } // enqueueScripts ()

        /**
         *  Set up our meta boxes.
         */
        public function addMetaBoxes () {
            $post = get_post ();
            $template = false;
            
            if (!empty ($post->post_name)) {
                $locations = Util::getTemplateLocations ($post->post_name);
                $template = Util::locateTemplate ($locations, '', true);
        
                if (!$template) {
                    add_meta_box ('fuse_block_template_meta', __ ('Template', 'fuse'), array ($this, 'renderTemplateMetaBox'), $this->getSlug (), 'normal', 'default');
                } // if ()
            } // if ()
             
            add_meta_box ('fuse_block_fields_meta', __ ('Block Fields', 'fuse'), array ($this, 'renderFieldsMetaBox'), $this->getSlug (), 'normal', 'default');
            
            if (empty ($template) === false) {
                add_meta_box ('fuse_block_template_location_meta', __ ('Template', 'fuse'), array ($this, 'templateLocationMeta'), $this->getSlug (), 'normal', 'default');
            } // if ()

            add_meta_box ('fuse_block_properties_meta', __ ('Block Properties', 'fuse'), array ($this, 'renderPropertiesMetaBox'), $this->getSlug (), 'side', 'default');
        } // addMetaBoxes ()
        
        /**
         * Display the template location below the title.
         */
        public function templateLocationMeta () {
            $post = get_post ();
        
            if (isset ($post->post_name) && empty ($post->post_name) === false) {
                $locations = Util::getTemplateLocations ($post->post_name, 'block');
                $template  = Util::locateTemplate ($locations, '', true);
            
                if ($template) {
                    $template_short  = str_replace (WP_CONTENT_DIR, basename (WP_CONTENT_DIR), $template);
                    $template_parts  = explode ('/', $template_short);
                    $filename = array_pop ($template_parts);
                    $template_breaks = '/'.trailingslashit (implode ('/', $template_parts));

                    ?>
                        <div class="edit-slug-box">
                            <strong><?php esc_html_e ('Template:', 'fuse'); ?></strong>
                            <?php echo esc_html ($template_breaks); ?><strong><?php echo esc_html ($filename); ?></strong>
                        </div>
                    <?php
                } // if ()
            } // if ()
        } // templateLocationMeta ()
        
        /**
         * Render the Block Fields meta box.
         *
         * @return void
         */
        public function renderFieldsMetaBox () {
            $post = get_post ();
            $block = new \Fuse\Block ($post->ID);
            
            do_action ('fuse_block_before_fields_list');
            
            ?>
                <div class="block-fields-list">
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th class="block-fields-sort"></th>
                                <th class="block-fields-label">
                                    <?php esc_html_e ('Field Label', 'fuse'); ?>
                                </th>
                                <th class="block-fields-name">
                                    <?php esc_html_e ('Field Name', 'fuse'); ?>
                                </th>
                                <th class="block-fields-control">
                                    <?php esc_html_e ('Field Type', 'fuse'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4">
                                    <div class="block-fields-rows">
                                        <p class="block-no-fields">
                                            <?php echo wp_kses_post (__ ('Click <strong>Add Field</strong> below to add your first field.', 'fuse')); ?>
                                        </p>
                                        <?php
                                            if (count ($block->fields) > 0) {
                                                foreach ($block->fields as $field) {
                                                    $this->_renderFieldsMetaBoxRow ($field, uniqid ());
                                                } // foreach ()
                                            } // if ()
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="block-fields-actions-add-field">
                    <button type="button" aria-label="Add Field" class="block-fields-action" id="block-add-field">
                        <span class="dashicons dashicons-plus"></span>
                        <?php esc_attr_e ('Add Field', 'fuse'); ?>
                    </button>
                    <script type="text/html" id="tmpl-field-repeater">
                        <?php
                            $args = array (
                                'name' => 'new-field',
                                'label' => __ ('New Field', 'fuse')
                            );
                            $this->_renderFieldsMetaBoxRow (new Field ($args));
                        ?>
                    </script>
                    <script type="text/html" id="tmpl-sub-field-rows">
                        <?php $this->_renderFieldsSubRows (); ?>
                    </script>
                </div>
            <?php
            
            do_action ('fuse_block_after_fields_list');
            wp_nonce_field ('fuse_save_fields', 'fuse_fields_nonce');
        } // renderFieldsMetaBox ()
        
        /**
         *  Render the Block Fields meta box.
         */
        public function renderPropertiesMetaBox () {
            $post = get_post ();
            $block = new \Fuse\Block ($post->ID);
            $icons = Util::getIcons ();
        
            if (!$block->icon) {
                $block->icon = 'block_lab';
            } // if ()
            
            ?>
                <p>
                    <label for="block-properties-slug">
                        <?php esc_html_e ('Slug:', 'fuse'); ?>
                    </label>
                    <input
                        name="post_name"
                        type="text"
                        id="block-properties-slug"
                        value="<?php echo esc_attr ($post->post_name); ?>" />
                </p>
                <p class="description">
                    <?php
                        esc_html_e ('Used to determine the name of the template file.', 'fuse');
                    ?>
                </p>
                <p>
                    <label for="block-properties-icon">
                        <?php esc_html_e ('Icon:', 'fuse'); ?>
                    </label>
                    <input
                        name="block-properties-icon"
                        type="hidden"
                        id="block-properties-icon"
                        value="<?php echo esc_attr ($block->icon); ?>" />
                    <span id="block-properties-icon-current">
                        <?php
                            if (array_key_exists ($block->icon, $icons)) {
                                echo wp_kses ($icons [$block->icon], Util::allowedSvgTags ());
                            } // if ()
                        ?>
                    </span>
                    <a class="button block-properties-icon-button" id="block-properties-icon-choose" href="#block-properties-icon-choose">
                        <?php esc_attr_e ('Choose', 'fuse'); ?>
                    </a>
                    <a class="button block-properties-icon-button" id="block-properties-icon-close" href="#">
                        <?php esc_attr_e ('Close', 'fuse'); ?>
                    </a>
                    <span class="block-properties-icon-select" id="block-properties-icon-select">
                        <?php
                            foreach ($icons as $icon => $svg) {
                                $selected = $icon === $block->icon ? 'selected' : '';
                                
                                printf (
                                    '<span class="icon %1$s" data-value="%2$s">%3$s</span>',
                                    esc_attr ($selected),
                                    esc_attr ($icon),
                                    wp_kses ($svg, Util::allowedSvgTags() )
                                );
                            } // foreach ()
                        ?>
                    </span>
                </p>
                <p>
                    <label for="block-properties-category">
                        <?php esc_html_e ('Category:', 'fuse'); ?>
                    </label>
                    <select name="block-properties-category" id="block-properties-category" class="block-properties-category">
                        <?php
                            /**
                             *  TODO:  Set up below....
                             */
                            $categories = get_block_categories ($post);
                        ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr ($category ['slug']); ?>" <?php selected ($category ['slug'], $block->category ['slug']); ?>>
                                <?php echo esc_html ($category ['title']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option disabled>────────</option>
                        <option value="__custom"><?php esc_html_e ('Custom Category', 'fuse'); ?></option>
                    </select>
                    <span class="block-properties-category-custom">
                        <label for="block-properties-category-name">
                            <?php esc_html_e ('New Category Name:', 'fuse'); ?>
                        </label>
                        <input
                            name="block-properties-category-name"
                            type="text"
                            id="block-properties-category-name"
                            value="" />
                    </span>
                </p>
                <p>
                    <label for="block-properties-keywords">
                        <?php esc_html_e ('Keywords:', 'fuse'); ?>
                    </label>
                    <input
                        name="block-properties-keywords"
                        type="text"
                        id="block-properties-keywords"
                        value="<?php echo esc_attr (implode (', ', $block->keywords)); ?>" />
                </p>
                <p class="description">
                    <?php esc_html_e ('A comma separated list of keywords, used when searching. Maximum of 3.', 'fuse'); ?>
                </p>
            <?php
            
            wp_nonce_field ('fuse_save_properties', 'fuse_properties_nonce');
        } // renderPropertiesMetaBox ()
        
        /**
         * Render the Block Template meta box.
         *
         * @return void
         */
        public function renderTemplateMetaBox () {
            $post = get_post ();
            
            $template = get_stylesheet_directory ().'/templates/blocks/'.$post->post_name.'.php';
            $template_short = str_replace (WP_CONTENT_DIR, basename (WP_CONTENT_DIR), $template);
            $template_parts = explode ('/', $template_short);
            $filename = array_pop ($template_parts);
            $template_breaks = '/'.trailingslashit (implode ('/<wbr>', $template_parts));
            ?>
                <div class="template-notice">
                    <h3>✔️ <?php esc_html_e ('Next step: Create a block template.', 'fuse'); ?></h3>
                    
                    <p>
                        <?php esc_html_e ('To display this block, Fuse will look for this template file in your theme:', 'fuse'); ?>
                    </p>

                    <p class="template-location">
                        <span class="path"><?php echo wp_kses ($template_breaks, array ('wbr' => array ())); ?></span>
                        <a class="filename" data-tooltip="<?php esc_attr_e ('Click to copy.', 'fuse'); ?>" href="#"><?php echo esc_html ($filename); ?></a>
                        <span class="click-to-copy">
                            <input type="text" readonly="readonly" value="<?php echo esc_html ($filename); ?>" />
                        </span>
                    </p>
                    
                    <p>
                        <strong><?php esc_html_e ('Learn more:', 'fuse'); ?></strong>
                        <?php
                            /**
                             *  TODO: Set these up to go to the right locations...
                             */
                            echo wp_kses_post (
                                sprintf (
                                    '<a href="%1$s" target="_blank">%2$s</a> | ',
                                    // 'https://getblocklab.com/docs/get-started/add-a-block-lab-block-to-your-website-content/',
                                    '#',
                                    esc_html__ ('Block Templates', 'fuse')
                                )
                            );
                            echo wp_kses_post (
                                sprintf (
                                    '<a href="%1$s" target="_blank">%2$s</a>',
                                    // 'https://getblocklab.com/docs/functions/',
                                    '#',
                                    esc_html__ ('Template Functions', 'fuse')
                                )
                            );
                        ?>
                    </p>
                </div>
            <?php
        } // renderTemplateMetaBox ()

        /**
         *  Save block meta boxes as a json blob in post content.
         *
         *  @param array $data An array of slashed post data.
         *
         *  @return array
         */
        public function insertBlock ($data) {
            if (isset ($_POST ['post_ID'])) {
                $post_id = sanitize_key ($_POST ['post_ID']);
        
                // Exits script depending on save status.
                if (wp_is_post_autosave ($post_id) === false && wp_is_post_revision ($post_id) == false && $this->getSlug () == $data ['post_type']) {
                    check_admin_referer ('fuse_save_fields', 'fuse_fields_nonce');
                    check_admin_referer ('fuse_save_properties', 'fuse_properties_nonce');
                
                    // Strip encoded special characters, like 🖖 (%f0%9f%96%96).
                    $data ['post_name'] = preg_replace ('/%[a-f|0-9][a-f|0-9]/', '', $data ['post_name']);
                
                    // Sanitize_title() allows underscores, but register_block_type doesn't.
                    $data ['post_name'] = str_replace ('_', '-', $data ['post_name']);
                
                    // If only special characters were used, it's possible the post_name is now empty.
                    if ('' === $data ['post_name']) {
                        $data ['post_name'] = $post_id;
                    } // if ()
                
                    // Register_block_type doesn't allow slugs starting with a number.
                    if (is_numeric ($data ['post_name'][0])) {
                         $data ['post_name'] = 'block-'.$data ['post_name'];
                    } // if ()
                
                    // Make sure the block slug is still unique.
                    $data ['post_name'] = wp_unique_post_slug (
                        $data ['post_name'],
                        $post_id,
                        $data ['post_status'],
                        $data ['post_type'],
                        $data ['post_parent']
                    );
                
                    $block = new \Fuse\Block ();
                
                    // Block name.
                    $block->name = sanitize_key ($data ['post_name']);
                    
                    if ('' === $block->name) {
                        $block->name = $post_id;
                    } // if ()
                
                    // Block title.
                    $block->title = sanitize_text_field (wp_unslash ($data ['post_title']));
                    
                    if ('' === $block->title) {
                        $block->title = $post_id;
                    } // if ()
                
                    // Block excluded post types.
                    if (isset ($_POST ['block-excluded-post-types'])) {
                        $excluded = sanitize_text_field (wp_unslash ($_POST ['block-excluded-post-types'] ));
                        
                        if (!empty ($excluded)) {
                            $block->excluded = explode (',', $excluded);
                        } // if ()
                    } // if ()
                
                    // Block icon.
                    if (isset ($_POST ['block-properties-icon'])) {
                        $block->icon = sanitize_key ($_POST ['block-properties-icon']);
                    } // if()
                
                    // Block category.
                    if (isset ($_POST ['block-properties-category'])) {
                        $category_slug = sanitize_key ($_POST ['block-properties-category']);
                        
                        /**
                         *TOOD: Figure this out!!!
                         */
                        $categories = get_block_categories (get_post ());
                
                        if ('__custom' === $category_slug && isset ($_POST ['block-properties-category-name'])) {
                            $category = array (
                                'slug' => sanitize_key ($_POST ['block-properties-category-name']),
                                'title' => sanitize_text_field (wp_unslash ($_POST ['block-properties-category-name'])),
                                'icon' => NULL
                            );
                        } // if ()
                        else {
                            $category_slugs = wp_list_pluck ($categories, 'slug');
                            $category_key = array_search ($category_slug, $category_slugs, true);
                            $category = $categories [$category_key];
                        } // else
                
                        if (!$category) {
                            $category = isset ($categories [0]) ? $categories [0] : '';
                        } // if ()
                
                        $block->category = $category;
                    } // if ()
                
                    // Block keywords.
                    if (isset ($_POST ['block-properties-keywords'])) {
                        $keywords = sanitize_text_field (wp_unslash ($_POST ['block-properties-keywords']));
                        $keywords = explode (',', $keywords);
                        $keywords = array_map ('trim', $keywords);
                        $keywords = array_slice ($keywords, 0, 3);

                        $block->keywords = $keywords;
                    } // if ))
                
                    // Block fields.
                    if (isset ($_POST ['block-fields-name']) && is_array ($_POST ['block-fields-name'])) {
                        // We loop through this array and sanitize its content according to the content type.
                        $fields = wp_unslash ($_POST ['block-fields-name']);
                        
                        foreach ($fields as $key => $name) {
                            // Field name and order.
                            $field_config = array (
                                'name' => sanitize_key ($name)
                            );
                
                            // Field label.
                            if (isset ($_POST ['block-fields-label'][$key])) {
                                $field_config ['label'] = sanitize_text_field (wp_unslash ($_POST ['block-fields-label'][$key]));
                            } // if ()
                
                            // Field control.

                            if (isset ($_POST ['block-fields-control'][$key])) {
                                $field_config ['control'] = sanitize_text_field (wp_unslash ($_POST ['block-fields-control'][$key]));
                            } // if ()
                
                            // Field type.
                            if (isset ($field_config ['control'] ) && isset ($this->controls [$field_config ['control']])) {
                                $field_config ['type'] = $this->controls [$field_config ['control']]->type;
                            } // if ()

                            if (isset ($field_config ['control']) && isset ($this->controls [$field_config ['control']])) {
                                $control = $this->controls [$field_config ['control']];
                                
                                foreach ($control->settings as $setting) {
                                    $value = false;
                
                                    if (isset ($_POST ['block-fields-settings'][$key][$setting->name])) {
                                        $value = $_POST ['block-fields-settings'][$key][$setting->name];
                                        $value = wp_unslash ($value);
                                    } // if ()
                
                                    // Sanitize the field options according to their type.
                                    if (is_callable ($setting->sanitise)) {
                                        $value = call_user_func ($setting->sanitise, $value);
                                    } // if ()
                
                                    // Validate the field options according to their type.
                                    if (is_callable ($setting->validate)) {
                                        $value = call_user_func (
                                            $setting->validate,
                                            $value,
                                            $field_config ['settings']
                                        );
                                    } // if ()
                
                                    $field_config ['settings'][$setting->name] = $value;
                
                                    $field = new Field ($field_config);
                                } // foerach ()
                            } //if ()
                            else {
                                $field = new Field ($field_config);
                            } //else
                
                            /*
                             * Sub-Fields
                             * If there's a "block-fields-parent" input, include the current field in a "sub-fields" field setting
                             * for the specified parent.
                             */
                            if (!empty ($_POST ['block-fields-parent'][$key])) {
                                $parent_uid = sanitize_key ($_POST ['block-fields-parent'][$key]);
                
                                // The parent's name should have been submitted.
                                if (isset ($fields [$parent_uid])) {
                                    $parent = $fields [$parent_uid];
                
                                    // The parent field should be set by now. We expect it to always precede the child field.
                                    if (isset ($block->fields [$parent])) {
                                        if (!isset ($block->fields [$parent]->settings ['sub_fields'])) {
                                            $block->fields [$parent]->settings ['sub_fields'] = array ();
                                        } // if ()
                    
                                        $field->settings ['parent'] = $parent;
                                        $field->order = count ($block->fields [$parent]->settings ['sub_fields']);
                    
                                        $block->fields [$parent]->settings ['sub_fields'][ $name ] = $field;
                                    } // if ()
                                } // if ()
                            } // if ()
                            else {
                                $field->order = count ($block->fields);
                
                                $block->fields [$name] = $field;
                            } // else
                        } // foreach ()
                    } // if ()
                    
                    $data ['post_content'] = wp_slash ($block->toJson ());
                }  // if ()
            } // if ()
        
            return $data;
        } // insertBlock ()

        /**
         * Change the default "Enter Title Here" placeholder on the edit post
         * screen.
         *
         * @param string $title Placeholder text. Default 'Enter title here'.
         *
         * @return string
         */
        public function postTitlePlaceholder ($title) {
            $screen = get_current_screen ();
        
            if (is_object ($screen) && $this->getSlug () === $screen->post_type) {
                $title = __ ('Enter block name', 'fuse');
            } // if ()
        
            return $title;
        } // postTitlePlaceholder ()

        /**
         *  Displays an option for editing the post type that this block appears
         *  on.
         */
        public function postTypeCondition () {
            $screen = get_current_screen ();
        
            if (is_object ($screen) && $this->getSlug () == $screen->post_type) {
                $post_types = get_post_types (array (
                    array (
                        'show_in_rest' => true,
                        'show_in_menu' => true
                    ),
                    'objects'
                ));
        
                $post_types = array_filter ($post_types,
                    function ($post_type) {
                        return post_type_supports ($post_type->name, 'editor');
                    }
                );
        
                $block = new \Fuse\Block (get_the_ID ());
                ?>
                <div class="block-lab-pub-section hide-if-no-js">
                    <?php esc_html_e( 'Post Types:', 'block-lab' ); ?> <span class="post-types-display"></span>
                    <a href="#post-types-select" class="edit-post-types" role="button">
                        <span aria-hidden="true"><?php esc_html_e( 'Edit', 'block-lab' ); ?></span>
                    </a>
                    <input type="hidden" value="<?php echo esc_attr( implode( ',', $block->excluded ) ); ?>" name="block-excluded-post-types" id="block-excluded-post-types" />
                    <div class="post-types-select">
                        <div class="post-types-select-items">
                            <?php
                            foreach ( $post_types as $post_type ) {
                                ?>
                                <input type="checkbox" id="block-post-type-<?php echo esc_attr( $post_type->name ); ?>" value="<?php echo esc_attr( $post_type->name ); ?>">
                                <label for="block-post-type-<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->label ); ?></label>
                                <br />
                                <?php
                            }
                            ?>
                        </div>
                        <a href="#post-types" class="save-post-types button"><?php esc_html_e( 'OK', 'block-lab' ); ?></a>
                        <a href="#post-types" class="button-cancel"><?php esc_html_e( 'Cancel', 'block-lab' ); ?></a>
                    </div>
                </div>
                <?php
            } // if ()
        } // postTypeCondition ()

        /**
         * Gets the field value to be made available or echoed on the front-end template.
         *
         * Gets the value based on the control type.
         * For example, a 'user' control can return a WP_User, a string, or false.
         * The $echo parameter is whether the value will be echoed on the front-end template,
         * or simply made available.
         *
         * @param mixed  $value The field value.
         * @param string $control The type of the control, like 'user'.
         * @param bool   $echo Whether or not this value will be echoed.
         * @return mixed $value The filtered field value.
         */
        public function getFieldValue ($value, $control, $echo) {
            if (isset ($this->controls [$control]) && method_exists ($this->controls [$control], 'validate')) {
                $value = call_user_func (array ($this->controls [$control], 'validate'), $value, $echo);
            } // if ()

            return $value;
        } // getFieldValue ()

        /**
         * Hide the Quick Edit row action.
         *
         * @param array $actions An array of row action links.
         *
         * @return array
         */
        public function pageRowActions ($actions = array ()) {
            $post = get_post ();
        
            // Abort if the post type is incorrect.
            if ($this->getSlug () == $post->post_type) {
                // Remove the Quick Edit link.
                if (isset ($actions ['inline hide-if-no-js'])) {
                    unset ($actions ['inline hide-if-no-js']);
                } // if ()
        
                // Add the Export link.
                $export = array (
                    'export' => sprintf (
                        '<a href="%1$s" aria-label="%2$s">%3$s</a>',
                        add_query_arg (array ('export' => $post->ID)),
                        sprintf (
                            __( 'Export %1$s', 'fuse'),
                                get_the_title ($post->ID)
                        ),
                        __ ('Export', 'block-lab')
                    )

                );
        
                $actions = array_merge (
                    array_slice ($actions, 0, 1),
                    $export,
                    array_slice ($actions, 1)
                );
            } // if ()
        
            return $actions;
        } // pageRowActions ()

        /**
         * Remove Edit from the Bulk Actions menu
         *
         * @param array $actions An array of bulk actions.
         *
         * @return array
         */
        public function bulkActions ($actions) {
            unset ($actions ['edit']);
            $actions ['export'] = __ ('Export', 'fuse');
        
            return $actions;
        } // bulkActions ()

        /**
         *  Change the columns in the Custom Blocks list table
         *
         *  @param array $columns An array of column name ⇒ label. The name is
         *  passed to functions to identify the column.
         *
         *  @return array
         */
        public function adminListColumns ($columns) {
            $new_columns = array (
                'cb' => $columns ['cb'],
                'title' => $columns ['title'],
                'icon' => __ ('Icon', 'fuse'),
                'template' => __ ('Template', 'fuse'),
                'category' => __ ('Category', 'fuse'),
                'keywords' => __ ('Keywords', 'fuse')
            );
            
            return $new_columns;
         } // adminListColumns ()
         
         /**
         * Output custom column data into the table
         *
         * @param string $column  The name of the column to display.
         * @param int    $post_id The ID of the current post.
         *
         * @return void
         */
        public function adminListValues ($column, $post_id) {
            switch ($column) {
                case'icon':
                    $block = new \Fuse\Block ($post_id);
                    $icons = Util::getIcons ();
        
                    if (property_exists ($block, 'icon') && array_key_exists ($block->icon, $icons)) {
                        printf (
                            '<span class="icon %1$s">%2$s</span>',
                            esc_attr ($block->icon),
                            wp_kses ($icons [$block->icon], Util::allowedSvgTags ())
                        );
                    } // if ()
                    
                    break;
                case 'template':
                    $block = new \Fuse\Block ($post_id);
                    
                    if (property_exists ($block, 'name')) {
                        $locations = Util::getTemplateLocations ($block->name, 'block');
                        $template = Util::locateTemplate ($locations, '', true);
            
                        if ($template) {
                            $template_short = str_replace (WP_CONTENT_DIR.'/themes/', '', $template);
                            $template_parts = explode ('/', $template_short);
                            $template_breaks = implode ('/', $template_parts);
                            
                            echo wp_kses (
                                '<code>'.$template_breaks.'</code>',
                                array (
                                    'code' => array (),
                                    'wbr'  => array ()
                                )
                            );
                        } // if ()
                        else {
                            echo '<span class="admin-bold admin-red">'.__ ('No template found.', 'fuse').'</span>';
                        } // else
                    } // if ()
                    else {
                        echo '<span class="admin-bold admin-red">'.__ ('No template found.', 'fuse').'</span>';
                    } // else
                    
                    break;
                case 'category':
                    $block = new \Fuse\Block ($post_id);
                    
                    if (property_exists ($block, 'category')) {
                        echo esc_html ($block->category ['title']);
                    } // if ()
                    
                    break;
                case 'keywords':
                    $block = new \Fuse\Block ($post_id);
                    
                    if (property_exists ($block, 'keywords')) {
                        echo esc_html (implode (', ', $block->keywords));
                    } // if ()
                    
                    break;
            } // switch ()
        } // adminListValues ()

        /**
         *  Handle the Export of a single block.
         */
        public function rowExport () {
            $post_id = filter_input (INPUT_GET, 'export', FILTER_SANITIZE_NUMBER_INT);
        
            if ($post_id > 0 && current_user_can ('block_lab_read_block', $post_id)) {
                $this->_export (array ($post_id));
            } // if ()
        } // rowExport ()
        
        /**
         *  Handle Exporting blocks via Bulk Actions
         *
         *  @param string $redirect Location to redirect to after the bulk action is completed.
         *  @param string $action The action to handle.
         *  @param array  $post_ids The IDs to handle.
         *
         *  @return string
         */
        public function bulkExport ($redirect, $action, $post_ids) {
            if ('export' == $action) {
                $this->_export ($post_ids);
        
                $redirect = add_query_arg ('bulk_export', count ($post_ids), $redirect);
            } // if ()

            return $redirect;
        } // bulkExport ()

        /**
         *  Ajax response for fetching field settings.
         */
        public function ajaxFieldSettings () {
            wp_verify_nonce ('fuse_field_options_nonce');
        
            if (!isset ($_POST ['control']) || !isset ($_POST ['uid'])) {
                wp_send_json_error ();
            } // if ()
            else {
                $control = sanitize_key ($_POST ['control']);
                $uid = sanitize_key ($_POST ['uid']);
        
                ob_start ();
                
                $field = new Field (array ('control' => $control));
        
                if (isset ($_POST ['parent'])) {
                    $field->settings ['parent'] = sanitize_key ( $_POST['parent']);
                } // if ()
        
                $this->_renderFieldSettings ($field, $uid);
                $data ['html'] = ob_get_clean ();
        
                if ('' === $data ['html']) {
                    wp_send_json_error ();
                } // if ()
            } // else

            wp_send_json_success( $data );
            
            die ();
        } // ajaxFieldSettings ()

        /**
         *  Register the controls.
         */
        public function registerControls () {
            $control_names = array (
                'text',
                'textarea',
                'url',
                'email',
                'number',
                'colour',
                'image',
                'select',
                'multiselect',
                'toggle',
                'range',
                'checkbox',
                'radio',
                'repeater',
                'post',
                'rich_text',
                'classic_text',
                'taxonomy',
                'user'
            );
        
            foreach ($control_names as $control_name) {
                $control = $this->_getControl ($control_name);
                
                if ($control) {
                    $controls [$control->name] = $control;
                } // if ()
            } // foreach ()
        
            $this->controls = apply_filters ('fuse_block_controls', $controls);
        } // registerControls ()
        
        /**
         *     aunch the blocks inside Gutenberg.
         */
        public function editorAssets () {
            wp_enqueue_script (
                'fuse-block-blocks',
                FUSE_BASE_URL.'/assets/javascript/block/editor.js',
                array (
                    'lodash',
                    'wp-api-fetch',
                    'wp-block-editor',
                    'wp-blocks',
                    'wp-components',
                    'wp-compose',
                    'wp-data',
                    'wp-deprecated',
                    'wp-editor',
                    'wp-element',
                    'wp-hooks',
                    'wp-html-entities',
                    'wp-keycodes'
                ),
                false,
                true
            );
    
            // Add dynamic Gutenberg blocks.
            wp_add_inline_script ('fuse-block-blocks', 'const fuseBlocks = '.wp_json_encode ($this->_blocks), 'before');
    
            // Enqueue optional editor only styles.
            wp_enqueue_style ('fuse-block-editor-css', FUSE_BASE_URL.'/assets/css/block/editor.css');
    
            $block_names = wp_list_pluck ($this->_blocks, 'name');
    
            foreach ($block_names as $block_name) {
                $this->_enqueueBlockStyles ($block_name, array ('preview', 'block'));
            } // foraech ()
    
            $this->_enqueueGlobalStyles ();
    
            // Used to conditionally show notices for blocks belonging to an author.
            $author_blocks = get_posts (array (
                'author' => get_current_user_id (),
                'post_type' => 'block_lab',
                // We could use -1 here, but that could be dangerous. 99 is more than enough.
                'posts_per_page' => 99
            ));
    
            $author_block_slugs = wp_list_pluck ($author_blocks, 'post_name');
    
            // Used to conditionally exclude blocks from certain post types.
            $post = get_post ();
            $post_type = $post->post_type;
    
            wp_localize_script (
                'fuse-block-blocks',
                'fuse',
                array (
                    'authorBlocks' => $author_block_slugs,
                    'postType' => $post_type
                )
            );
        } // editorAssets ()
        
        /**
         * Register custom block categories.
         *
         * @param array $categories Array of block categories.
         *
         * @return array
         */
        public function registerCategories ($categories) {
            foreach ($this->_blocks as $block_config) {
                if (isset ($block_config ['category'])) {
                    /*
                     * This is a backwards compatibility fix.
                     *
                     * Block categories used to be saved as strings, but were always included in
                     * the default list of categories, so it's safe to skip them.
                     */
                    if (!is_array ($block_config ['category']) || empty ($block_config ['category'])) {
                        continue;
                    } // if ()
        
                    if (!in_array ($block_config ['category'], $categories, true)) {
                        $categories [] = $block_config ['category'];
                    } // if ()
                } // if ()
            } // foreach ()
    
            return $categories;
        } // registerCategories ()
        
        
        
        
        /**
         * *  Renders the block provided a template is provided.
         *
         *  @param Block $block The block to render.
         *  @param array $attributes Attributes to render.
         *
         *  @return mixed
         */
        protected function _renderBlockTemplate ($block, $attributes) {
            $type = 'block';
    
            // This is hacky, but the editor doesn't send the original request along.
            $context = filter_input (INPUT_GET, 'context', FILTER_SANITIZE_STRING);
    
            if ('edit' === $context) {
                $type = array ('preview', 'block');
            } // if ()
    
            if (!is_admin ()) {
                /**
                 * The block has been added, but its values weren't saved (not
                 * even the defaults). This is a phenomenon unique to frontend
                 * output, as the editor fetches its attributes from the form
                 * fields themselves.
                 */
                $missing_schema_attributes = array_diff_key ($block->fields, $attributes);
                
                foreach ($missing_schema_attributes as $attribute_name => $schema) {
                    if (isset ($schema->settings ['default'])) {
                        $attributes [$attribute_name] = $schema->settings ['default'];
                    } // if ()
                } // foreach ()
    
                // Similar to the logic above, populate the Repeater control's sub-fields with default values.
                foreach ($block->fields as $field) {
                    if (isset ($field->settings ['sub_fields']) && isset ($attributes [$field->name]['rows'])) {
                        $sub_field_settings = $field->settings ['sub_fields'];
                        $rows = $attributes [$field->name]['rows'];
    
                        // In each row, apply a field's default value if a value doesn't exist in the attributes.
                        foreach ($rows as $row_index => $row) {
                            foreach ($sub_field_settings as $sub_field_name => $sub_field) {
                                if (!isset ($row [$sub_field_name]) && isset ($sub_field_settings [$sub_field_name]->settings ['default'])) {
                                    $rows [$row_index][$sub_field_name] = $sub_field_settings [$sub_field_name]->settings ['default'];
                                } // if ()
                            } // foraech ()
                        } // foreach ()
    
                        $attributes [$field->name]['rows'] = $rows;
                    } // if ()
                } // foreach ()
    
                $this->_enqueueBlockStyles ($block->name, 'block');
    
                /**
                 * The wp_enqueue_style function handles duplicates, so we don't
                 * need to worry about multiple blocks loading the global styles
                 * more than once.
                 */
                $this->_enqueueGlobalStyles ();
            } // if ()
    
            $this->data ['attributes'] = $attributes;
            $this->data ['config'] = $block;
            
            Util::$data ['attributes'] = $attributes;
            Util::$data ['config'] = $block;
    
            if (!is_admin () && (!defined ('REST_REQUEST') || !REST_REQUEST) && !wp_doing_ajax ()) {
                /**
                 *  Runs in the 'render_callback' of the block, and only on the
                 *  front-end, not in the editor.
                 *
                 *  The block's name (slug) is in $block->name. If a block
                 *  depends on a JavaScript file, this action is a good place to
                 *  call wp_enqueue_script(). In that case, pass true as the 5th
                 *  argument ($in_footer) to wp_enqueue_script().
                 *
                 *  @param Block $block The block that is rendered.
                 *  @param array $attributes The block attributes.
                 */
                do_action ('fuse_block_render_template', $block, $attributes);
    
                /**
                 *  Runs in a block's 'render_callback', and only on the
                 *  front-end. Same as the action above, but with a dynamic
                 *  action name that has the block name.
                 *
                 *  @param Block $block The block that is rendered.
                 *  @param array $attributes The block attributes.
                 */
                do_action ('block_lab_render_template_'.$block->name, $block, $attributes);
            } // if ()
    
            ob_start ();
            $this->_blockTemplate ($block->name, $type);
            $output = ob_get_clean ();
    
            return $output;
        } //  _renderBlockTemplate ()
        
        /**
         *  Enqueues styles for the block.
         *
         *  @param string $name The name of the block (slug as defined in UI).
         *  @param string|array $type The type of template to load.
         */
        protected function _enqueueBlockStyles ($name, $types = 'block') {
            $locations = array ();
            
            if (is_array ($types) === false) {
                $types = array ($types);
            } // if ()
    
            foreach ($types as $type) {
                $locations = array_merge (
                    $locations,
                    Util::getStylesheetLocations ($name, $type)
                );
            } // foreach ()
    
            $stylesheet_path = Util::locateTemplate ($locations);
            $stylesheet_url = Util::getUrlFromPath ($stylesheet_path);
    
            /**
             * Enqueue the stylesheet, if it exists. The wp_enqueue_style
             * function handles duplicates, so we don't need to worry about the
             * same block loading its stylesheets more than once.
             */
            if (!empty ($stylesheet_url)) {
                wp_enqueue_style (
                    'block-lab__block-'.$name,
                    $stylesheet_url,
                    array (),
                    wp_get_theme ()->get ('Version')
                );
            } // if ()
        } // _enqueueBlockStyles ()
        
        /**
         * Enqueues global block styles.
         */
        protected function _enqueueGlobalStyles () {
            $locations = array (
                'blocks/css/blocks.css',
                'blocks/blocks.css'
            );
    
            $stylesheet_path = Util::locateTemplate ($locations);
            $stylesheet_url = Util::getUrlFromPath ($stylesheet_path);
    
            /**
             * Enqueue the stylesheet, if it exists.
             */
            if (!empty ($stylesheet_url)) {
                wp_enqueue_style (
                    'fuse_blocks__global-styles',
                    $stylesheet_url,
                    array (),
                    wp_get_theme ()->get ('Version')
                );
            } // if ()
        } // _enqueueGlobalStyles ()
        
        /**
         *  Loads a block template to render the block.
         *
         *  @param string $name The name of the block (slug as defined in UI).
         *  @param string|array $type The type of template to load.
         */
        protected function _blockTemplate ($name, $types = 'block') {
            // Loading async it might not come from a query, this breaks load_template().
            global $wp_query;
    
            // So lets fix it.
            if (empty ($wp_query)) {
                $wp_query = new \WP_Query ();
            } // if ()
            
            if (is_array ($types) === false) {
                $types = array ($types);
            } // if ()
    
            $located = '';
    
            foreach ($types as $type) {
                $templates = Util::getTemplateLocations ($name, $type);
                $located = Util::locateTemplate ($templates);
    
                if (!empty ($located)) {
                    break;
                } // if ()
            } // foreach ()
    
            if (!empty ($located)) {
                $theme_template = apply_filters ('fuse_block_override_theme_template', $located);
    
                // This is not a load once template, so require_once is false.
                load_template ($theme_template, false);
            } // if ()
            else {
                if (!current_user_can ('edit_posts') || !isset ($templates [0])) {
                    return;
                } // if ()
                
                // Hide the template not found notice on the frontend, unless WP_DEBUG is enabled.
                if (!is_admin () && !(defined ('WP_DEBUG') && WP_DEBUG)) {
                    return;
                } // if ()
                
                printf ('<div class="notice notice-warning">%s</div>',wp_kses_post (
                    // Translators: Placeholder is a file path.
                    sprintf (__ ('Template file %s not found.', 'fuse'), '<code>'.esc_html ($templates [0]).'</code>')
                ));
            } // else
        } // _blockTemplate ()
        
        /**
         *  Gets block attributes.
         *
         *  @param Block $block The block to get attributes from.
         *
         *  @return array
         */
        protected function _getBlockAttributes ($block) {
            $attributes = array ();
    
            // Default Editor attributes (applied to all blocks).
            $attributes ['className'] = array ('type' => 'string');
    
            foreach ($block->fields as $field_name => $field) {
                $attributes = $this->_getAttributesFromField ($attributes, $field_name, $field);
            } // foreach ()
    
            /**
             *  Filters a given block's attributes.
             *
             *  These are later passed to register_block_type() in
             *  $args['attributes']. Removing attributes here can cause 'Error
             *  loading block...' in the editor.
             *
             *  @param array[] $attributes The attributes for a block.
             *
             *  @param array   $block      Block data, including its name at
             *  $block['name'].
             */
            return apply_filters ('fuse_get_block_attributes', $attributes, $block);
        } // _getBlockAttributes ()
    
        /**
         *  Sets the field values in the attributes, enabling them to appear in
         *  the block.
         *
         *  @param array  $attributes The attributes in which to store the field
         *  value.
         *  @param string $field_name The name of the field, like 'home-hero'.
         *  @param Field  $field      The Field to set the attributes from.
         *
         *  @return array $attributes The attributes, with the new field value
         *  set.
         */
        protected function _getAttributesFromField ($attributes, $field_name, $field) {
            $attributes [$field_name] = array (
                'type' => $field->type
            );
    
            if (!empty ($field->settings ['default'])) {
                $attributes [$field_name]['default'] = $field->settings ['default'];
            } // if ()
    
            if ('array' === $field->type) {
                /**
                 * This is a workaround to allow empty array values. We unset
                 * the default value before registering the block so that the
                 * default isn't used to auto-correct empty arrays. This allows
                 * the default to be used only when creating the form.
                 */
                unset ($attributes [$field_name]['default']);
                
                $items_type = 'repeater' === $field->control ? 'object' : 'string';
                $attributes [$field_name]['items'] = array ('type' => $items_type);
            } // if ()
    
            return $attributes;
        } // _getAttributesFromField ()
        
        /**
         *  Gets the mapping of capabilities for the custom post type.
         *
         *  @return array An associative array of capability key => custom
         *  capability value.
         */
        protected function _getCapabilities () {
            return array (
                'edit_post' => 'fuse_edit_block',
                'edit_posts' => 'fuse_edit_blocks',
                'edit_others_posts' => 'fuse_edit_others_blocks',
                'publish_posts' => 'fuse_publish_blocks',
                'read_post' => 'fuse_read_block',
                'read_private_posts' => 'fuse_read_private_blocks',
                'delete_post' => 'fuse_delete_block'
            );
        } // _getCapabilities ()
        
        /**
         * Render a single Field as a row.
         *
         * @param Field $field      The Field containing the options to render.
         * @param mixed $uid        A unique ID to used to unify the HTML name, for, and id attributes.
         * @param mixed $parent_uid The parent's unique ID, if the field has a parent.
         *
         * @return void
         */
        protected function _renderFieldsMetaBoxRow ($field, $uid = false, $parent_uid = false) {
            // Use a template placeholder if no UID provided.
            if (empty ($uid)) {
                $uid = '{{ data.uid }}';
            } // if ()

            ?>
                <div class="block-fields-row" data-uid="<?php echo esc_attr ($uid); ?>">
                    <div class="block-fields-row-columns">
                        <div class="block-fields-sort">
                            <span class="block-fields-sort-handle"></span>
                        </div>
                        <div class="block-fields-label">
                            <a class="row-title" href="javascript:" id="block-fields-label_<?php echo esc_attr ($uid); ?>">
                                <?php echo esc_html ($field->label); ?>
                            </a>
                            <div class="block-fields-actions">
                                <a class="block-fields-actions-edit" href="javascript:">
                                    <?php esc_html_e ('Edit', 'fuse'); ?>
                                </a>
                                &nbsp;|&nbsp;
                                <a class="block-fields-actions-duplicate" href="javascript:">
                                    <?php esc_html_e ('Duplicate', 'fuse'); ?>
                                </a>
                                &nbsp;|&nbsp;
                                <a class="block-fields-actions-delete" href="javascript:">
                                    <?php esc_html_e ('Delete', 'fuse'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="block-fields-name" id="block-fields-name_<?php echo esc_attr ($uid); ?>">
                            <code id="block-fields-name-code_<?php echo esc_attr ($uid); ?>"><?php echo esc_html ($field->name); ?></code>
                        </div>
                        <div class="block-fields-control" id="block-fields-control_<?php echo esc_attr ($uid); ?>">
                            <?php echo esc_html ($this->controls [$field->control]->label); ?>
                        </div>
                    </div>
                    <div class="block-fields-edit">
                        <table class="widefat">
                            <tr class="block-fields-edit-label">
                                <td class="spacer"></td>
                                <th scope="row">
                                    <label for="block-fields-edit-label-input_<?php echo esc_attr ($uid); ?>">
                                        <?php esc_html_e ('Field Label', 'fuse'); ?>
                                    </label>
                                    <p class="description">
                                        <?php
                                            esc_html_e (
                                                'A label describing your block\'s custom field.',
                                                'fuse'
                                            );
                                        ?>
                                    </p>
                                </th>
                                <td>
                                    <input
                                        name="block-fields-label[<?php echo esc_attr ($uid); ?>]"
                                        type="text"
                                        id="block-fields-edit-label-input_<?php echo esc_attr ($uid); ?>"
                                        class="regular-text"
                                        value="<?php echo esc_attr ($field->label); ?>"
                                        data-sync="block-fields-label_<?php echo esc_attr ($uid); ?>"
                                    />
                                </td>
                            </tr>
                            <tr class="block-fields-edit-name">
                                <td class="spacer"></td>
                                <th scope="row">
                                    <label for="block-fields-edit-name-input_<?php echo esc_attr ($uid); ?>">
                                        <?php esc_html_e ('Field Name', 'fuse'); ?>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e ('Single word, no spaces.', 'fuse'); ?>
                                    </p>
                                </th>
                                <td>
                                    <input
                                        name="block-fields-name[<?php echo esc_attr ($uid); ?>]"
                                        type="text"
                                        id="block-fields-edit-name-input_<?php echo esc_attr ($uid); ?>"
                                        class="regular-text"
                                        value="<?php echo esc_attr ($field->name); ?>"
                                        data-sync="block-fields-name-code_<?php echo esc_attr ($uid); ?>"
                                    />
                                </td>
                            </tr>
                            <tr class="block-fields-edit-control">
                                <td class="spacer"></td>
                                <th scope="row">
                                    <label for="block-fields-edit-control-input_<?php echo esc_attr ($uid); ?>">
                                        <?php esc_html_e ('Field Type', 'fuse'); ?>
                                    </label>
                                </th>
                                <td>
                                    <select
                                        name="block-fields-control[<?php echo esc_attr ($uid); ?>]"
                                        id="block-fields-edit-control-input_<?php echo esc_attr ($uid); ?>"
                                        data-sync="block-fields-control_<?php echo esc_attr ($uid); ?>">
                                        <?php
                                            $controls_for_select = $this->controls;
            
                                            $controls_for_select [$field->control] = $this->_getControl ($field->control);
            
                                            // Don't allow nesting repeaters inside repeaters.
                                            if (!empty ($field->settings ['parent'])) {
                                                unset ($controls_for_select ['repeater']);
                                            } // if ()
                                        ?>
                                        <?php foreach ($controls_for_select as $control_for_select): ?>
                                            <option
                                                value="<?php echo esc_attr ($control_for_select->name); ?>"
                                                <?php selected ($field->control, $control_for_select->name); ?>>
                                                <?php echo esc_html ($control_for_select->label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php $this->_renderFieldSettings ($field, $uid); ?>
                            <tr class="block-fields-edit-actions-close">
                                <td class="spacer"></td>
                                <th scope="row">
                                </th>
                                <td>
                                    <a class="button" title="<?php esc_attr_e ('Close Field', 'fuse'); ?>" href="javascript:">
                                        <?php esc_html_e ('Close Field', 'fuse'); ?>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                        if ('repeater' === $field->control) {
                            if (!isset ($field->settings ['sub_fields'])) {
                                $field->settings ['sub_fields'] = array ();
                            } //if ()
                            
                            $this->_renderFieldsSubRows ($field->settings ['sub_fields'], $uid);
                        } // if ()
                    ?>
                    <?php if ($parent_uid):  ?>
                        <input
                            type="hidden"
                            name="block-fields-parent[<?php echo esc_attr ($uid); ?>]"
                            value="<?php echo esc_attr ($parent_uid); ?>"
                        />
                    <?php endif; ?>
                </div>
            <?php
        } // _renderFieldsMetaBoxRow ()
        
        /**
         *  Render the actions row when adding a Repeater field.
         *
         *  @param Field[] $fields     The sub fields to render.
         *  @param mixed   $parent_uid The unique ID of the field's parent.
         */
        protected function _renderFieldsSubRows ($fields = array (), $parent_uid = false) {
            ?>
                <div class="block-fields-sub-rows">
                    <?php
                        if (!empty ($fields)) {
                            foreach ($fields as $field) {
                                $this->_renderFieldsMetaBoxRow ($field, uniqid (), $parent_uid);
                            } // foreach ()
                        } // if ()
                    ?>
                </div>
                <div class="block-fields-sub-rows-actions">
                    <p class="repeater-no-fields <?php echo esc_attr (empty ($fields) ? '' : 'hidden'); ?>">
                        <button type="button" aria-label="Add Sub-Field" id="block-add-sub-field">
                            <span class="dashicons dashicons-plus"></span>
                            <?php esc_attr_e ('Add your first Sub-Field', 'fuse'); ?>
                        </button>
                    </p>
                    <p class="repeater-has-fields <?php echo esc_attr (empty ($fields) ? 'hidden' : ''); ?>">
                        <button type="button" aria-label="Add Sub-Field" id="block-add-sub-field">
                            <span class="dashicons dashicons-plus"></span>
                            <?php esc_attr_e ('Add Sub-Field', 'fuse'); ?>
                        </button>
                    </p>
                </div>
            <?php
        } // _renderFieldSubRows ()
        
        /**
         *  Render the settings for a given field.
         *
         *  @param Field  $field The Field containing the options to render.
         *
         *  @param string $uid   A unique ID to used to unify the HTML name,
         *  for, and id attributes.
         */
        protected function _renderFieldSettings ($field, $uid) {
            if (isset ($this->controls [$field->control])) {
                $this->controls [$field->control]->renderSettings ($field, $uid);
            } // if ()
        } // _renderFieldSettings ()

        /**
         *  Gets an instantiated control.
         *
         *  @param string $control_name The name of the control.
         *  @return object|null The instantiated control, or null.
         */
        protected function _getControl ($control_name) {
            $control = NULL;
            
            if (isset ($this->controls [$control_name])) {
                $control = $this->controls [$control_name];
            } // if ()
            else {
                $class_name = str_replace ('_', '', ucwords ($control_name, '_'));
                
                $control_class = '\\Fuse\\Block\Control\\'.$class_name;
                
                if (class_exists ($control_class)) {
                    $control = new $control_class ();
                } // if ()
            } // else
            
            return $control;
        } // _getControl ()
        
        
        
        
        /**
         *  Export Blocks
         *
         *  @param array $post_ids The post IDs to export.
         */
        private function _export ($post_ids) {
            $blocks = array ();
        
            foreach ($post_ids as $post_id) {
                $post = get_post ($post_id);
        
                if ($post) {
                    // Check that the post content is valid JSON.
                    $block = json_decode ($post->post_content, true);
        
                    if (JSON_ERROR_NONE == json_last_error ()) {
                        $blocks = array_merge ($blocks, $block);
                    } // if ()
                } // if ()
            } // foreach ()
        
            // If only one block is being exported, use the block's slug as the filename.
            $filename = 'blocks.json';
            
            if (count ($post_ids) == 1) {
                $post = get_post ($post_ids [0]);
                $filename = $post->post_name.'.json';
            } // if ()
        
            // Output the JSON file.
            header ('Content-disposition: attachment; filename='.$filename);
            header ('Content-type:application/json;charset=utf-8');
            echo wp_json_encode ($blocks);
            
            die ();
        } // _export ()
        
    } // class Block