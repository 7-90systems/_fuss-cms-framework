<?php
    /**
     *  @package fusecms
     *  
     *  Set up our theme options.
     *
     *  @filter fuse_theme_supports Filter the themes 'theme supports'
     *  definitions.
     *  @filter fuse_css_dependencies Set the CSS dependencies
     *  @filter fuse_javascript_dependencies Set the JavaScript dependencies.
     *  @filter fuse_css_login_dependencies Set the CSS dependencies
     *  @filter fuse_javascript_login_dependencies Set the JavaScript dependencies.
     *  @filter fuse_css_admin_dependencies Set the CSS dependencies
     *  @filter fuse_javascript_admin_dependencies Set the JavaScript dependencies.
     *  @filter fuse_nav_menus Set up navigation menus to be registered.
     *  @filter fuse_sidebars Set up sidebars to be registered.
     *  @filter fuse_image_sizes Set up the additional image sizes.
     *  @filter fuse_register_shortcodes Set up the shortcodes to register.
     *  @filter fuse_before_enqueue_css Run before CSS is enqueued.
     *  @filter fuse_after_enqueue_css Run after CSS is enqueued
     *  @filter fuse_before_enqueue_javascript Run before JavaScript is enqueued.
     *  @filter fuse_after_enqueue_javascript Run after JavaScript is enqueued
     */
    
    namespace Fuse\Setup;
    
    use Fuse\Setup\Theme\ImageSize;
    use Fuse\Shortcode;
    
    
    class Theme {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            /**
             *  Set up the theme actions and filters.
             */
            
            // Theme Supports
            add_action ('after_setup_theme', array ($this, 'setThemeSupports'));
            
            // JavaScript and CSS files
            add_action ('wp_enqueue_scripts', array ($this, 'wpEnqueueScripts'));
            add_action ('login_enqueue_scripts', array ($this, 'loginEnqueueScripts'));
            add_action ('admin_enqueue_scripts', array ($this, 'adminEnqueueScripts'));
            
            // Menus
            add_action ('after_setup_theme', array ($this, 'registerNavMenus'));
            
            // Sidebars
            add_action ('widgets_init', array ($this, 'registerSidebars'));
            
            // Image Sizes
            add_action ('after_setup_theme', array ($this, 'registerImageSizes'));
            
            // Register shortcodes
            add_action ('after_setup_theme', array ($this, 'registerShortcodes'));

            // Set up our body class for the layout
            add_filter ('body_class', array ($this, 'bodyClass'));
            
            // Register assets
            $assets = new Assets ();
            
            // Remove extraneous <p> tags from shortcodes
            add_filter ('the_content', array ($this, 'removeShortcodeP'), PHP_INT_MAX);
        } // __construct ()
        
        
        
        
        /**
         *  Add in our theme supports calls.
         */
        public function setThemeSupports () {
            $supports = apply_filters ('fuse_theme_supports', array (
                'html5',
                'title-tag'
            ));
            
            foreach ($supports as $key => $args) {
                if (is_numeric ($key)) {
                    // Simple call
                    add_theme_support ($args);
                } // if ()
                else {
                    // Extended call
                    add_theme_support ($key, $args);
                } // else
            } // foreach ()
        } // setThemeSupports ()
        
        
        
        
        /**
         *  Enqueue our CSS and JavaScript files for the public site.
         */
        public function wpEnqueueScripts () {
            $this->_enqueueCss ();
            $this->_enqueueJavaScript ();
        } // wpEnqueueScripts ()
        
        /**
         *  Enqueue our CSS files.
         */
        protected function _enqueueCss () {
            do_action ('fuse_before_enqueue_css');
            
            $deps = array ();
            
            // Set up our assets
            wp_register_style ('mmenulight', FUSE_BASE_URL.'/assets/external/mmenu-light-master/dist/mmenu-light.css');
            wp_register_style ('superfish', FUSE_BASE_URL.'/assets/external/superfish-master/dist/css/superfish.css');
                wp_register_style ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/example1/colorbox.css');
            
            if (defined ('WP_DEBUG') && WP_DEBUG === true) {
                wp_register_style ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.css');
            } // if ()
            else {
                wp_register_style ('bxslider', FUSE_BASE_URL.'/assets/extrenal/bxslider-4-4.2.12/dist/jquery.bxslider.min.css');
            } // else
            
            $theme_base = trailingslashit (get_stylesheet_directory_uri ());
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                $parent_base = trailingslashit (get_template_directory_uri ());
                
                // Do we have an editor stylesheet?
                $editor_url = $parent_base.'assets/css/editor.css';
                
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'editor.css')) {
                    wp_register_style ('fuse_parent_theme_editor_stylesheet', $editor_url);
                    $deps [] = 'fuse_parent_theme_editor_stylesheet';
                    
                    add_editor_style ($editor_url);
                } // if ()
                
                wp_register_style ('fuse_parent_theme_stylesheet', $parent_base.'style.css');
                $deps [] = 'fuse_parent_theme_stylesheet';
            } // if ()
            
            // Do we have an editor stylesheet?
            $editor_url = $theme_base.'assets/css/editor.css';
            
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'editor.css')) {
                wp_register_style ('fuse_theme_editor_stylesheet', $editor_url);
                $deps [] = 'fuse_theme_editor_stylesheet';
                
                add_editor_style ($editor_url);
            } // if ()
            
            // Finalise dependencies
            $deps = apply_filters ('fuse_css_dependencies', $deps);
            wp_enqueue_style ('fuse_theme_stylesheet', $theme_base.'style.css', $deps);
            
            do_action ('fuse_after_enqueue_css');
        } // _enqueueCss ()
        
        /**
         *  Enqueue our JavaScript files
         */
        protected function _enqueueJavaScript () {
            do_action ('fuse_before_enqueue_javascript');
            
            // Set up our assets
            wp_register_script ('mmenulight', FUSE_BASE_URL.'/assets/external/mmenu-light-master/dist/mmenu-light.js', array ('jquery'));
            wp_register_script ('hoverintent', FUSE_BASE_URL.'/assets/external/superfish-master/dist/js/hoverIntent.js', array ('jquery'));
            
            if (defined ('WP_DEBUG') && WP_DEBUG === true) {
                wp_register_script ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.js', array ('jquery'));
                wp_register_script ('superfish', FUSE_BASE_URL.'/assets/external/superfish-master/dist/js/superfish.js', array ('jquery', 'hoverintent'));
                wp_register_script ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/jquery.colorbox.js', array ('jquery'));
            } // if ()
            else {
                wp_register_script ('bxslider', FUSE_BASE_URL.'/assets/external/bxslider-4-4.2.12/dist/jquery.bxslider.min.js', array ('jquery'));
                wp_register_script ('superfish', FUSE_BASE_URL.'/assets/external/superfish-master/dist/js/superfish.min.js', array ('jquery', 'hoverintent'));
                wp_register_script ('colorbox', FUSE_BASE_URL.'/assets/external/colorbox-master/jquery.colorbox.min.js', array ('jquery'));
            } // else
            
            $deps = array (
                'jquery'
            );
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'functions.js')) {
                    wp_register_script ('fuse_parent_theme_functons', trailingslashit (get_stylesheet_directory_uri ()).'assets/javascript/functions.js');
                    $deps [] = 'fuse_parent_theme_functions';
                } // if ()
            } // if ()
            
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'functions.js')) {
                $deps = apply_filters ('fuse_javascript_dependencies', $deps);
                wp_enqueue_script ('fuse_theme_functions', trailingslashit (get_template_directory_uri ()).'assets/javascript/functions.js', $deps);
            } // if ()
            
            do_action ('fuse_after_enqueue_javascript');
        } // _enqueueJavaScript ()
        
        
        
        
        /**
         *  Enqueue our CSS and JavaScript files for the login area.
         */
        public function loginEnqueueScripts () {
            $this->_loginEnqueueCss ();
            $this->_loginEnqueueJavaScript ();
        } // loginEnqueueScripts ()
        
        /**
         *  Enqueue our CSS files.
         */
        protected function _loginEnqueueCss () {
            $deps = array ();
            
            $theme_base = trailingslashit (get_stylesheet_directory_uri ());
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                $parent_base = trailingslashit (get_template_directory_uri ());
                
                // Do we have an login stylesheet?
                $editor_url = $parent_base.'assets/css/login.css';
                
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'login.css')) {
                    wp_enqueue_style ('fuse_parent_login_editor_stylesheet', $editor_url);
                } // if ()
            } // if ()
            
            // Do we have an login stylesheet?
            $editor_url = $theme_base.'assets/css/login.css';
            
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'login.css')) {
                $deps = apply_filters ('fuse_css_login_dependencies', $deps);
                wp_enqueue_style ('fuse_theme_login_stylesheet', $editor_url);
            } // if ()
        } // _loginEnqueueCss ()
        
        /**
         *  Enqueue our JavaScript files for the login area.
         */
        protected function _loginEnqueueJavaScript () {
            $deps = array (
                'jquery'
            );
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'login.js')) {
                    wp_enqueue_script ('fuse_parent_login_editor_functons', trailingslashit (get_stylesheet_directory_uri ()).'assets/javascript/login.js');
                } // if ()
            } // if ()
            
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'login.js')) {
                $deps = apply_filters ('fuse_javascript_login_dependencies', $deps);
                wp_enqueue_script ('fuse_login_functions', trailingslashit (get_template_directory_uri ()).'assets/javascript/login.js', $deps);
            } // if ()
        } // _loginEnqueueJavaScript ()
        
        
        
        
        /**
         *  Enqueue our CSS and JavaScript files for the admin area.
         */
        public function adminEnqueueScripts () {
            $this->_adminEnqueueCss ();
            $this->_adminEnqueueJavaScript ();
        } // adminEnqueueScripts ()
        
        /**
         *  Enqueue our CSS files.
         */
        protected function _adminEnqueueCss () {
            $deps = array ();
            
            $theme_base = trailingslashit (get_stylesheet_directory_uri ());
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                $parent_base = trailingslashit (get_template_directory_uri ());
                
                // Do we have an login stylesheet?
                $editor_url = $parent_base.'assets/css/admin.css';
                
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'admin.css')) {
                    $deps [] = 'fuse_parent_login_editor_stylesheet';
                    wp_register_style ('fuse_parent_login_editor_stylesheet', $editor_url);
                } // if ()
            } // if ()
            
            // Do we have an admin stylesheet?
            $editor_url = $theme_base.'assets/css/admin.css';
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'admin.css')) {
                $deps = apply_filters ('fuse_css_admin_dependencies', $deps);
                wp_register_style ('fuse_theme_login_stylesheet', $editor_url);
            } // if ()
            
            wp_enqueue_style ('fuse-core-admin', FUSE_BASE_URL.'/assets/css/admin.css', $deps);
        } // _adminEnqueueCss ()
        
        /**
         *  Enqueue our JavaScript files for the admin area.
         */
        protected function _adminEnqueueJavaScript () {
            $deps = array (
                'jquery'
            );
            
            // Are we using a child theme?
            if (is_child_theme ()) {
                if (file_exists (get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'admin.js')) {
                    wp_enqueue_script ('fuse_parent_login_editor_functons', trailingslashit (get_stylesheet_directory_uri ()).'assets/javascript/admin.js');
                } // if ()
            } // if ()
            
            if (file_exists (get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript'.DIRECTORY_SEPARATOR.'admin.js')) {
                $deps = apply_filters ('fuse_javascript_admin_dependencies', $deps);
                wp_enqueue_script ('fuse_login_functions', trailingslashit (get_template_directory_uri ()).'assets/javascript/admin.js', $deps);
            } // if ()
        } // _adminEnqueueJavaScript ()
        
        
        
        
        /**
         *  Register our navigation menus.
         */
        public function registerNavMenus () {
            $menus = apply_filters ('fuse_nav_menus', array (
                'primary' => __ ('Primary navigation menu', 'fuse')
            ));
            
            register_nav_menus ($menus);
        } // registerNavMenus ()
        
        
        
        
        /**
         *  Register our sidebars.
         */
        public function registerSidebars () {
            $sidebars = apply_filters ('fuse_sidebars', array (
                'default' => __ ('Default sidebar', 'fuse')
            ));
            
            foreach ($sidebars as $alias => $name) {
                if (is_array ($name)) {
                    $name ['id'] = $alias;
                    
                    register_sidebar ($name);
                } // if ()
                else {
                    register_sidebar (array (
                        'id' => $alias,
                        'name' => $name
                    ));
                } // else
            } // foreach ()
        } // registerSidebars ()
        
        
        
        
        /**
         *  Register our image sizes.
         *
         *  Any values passed into this array should be
         *  Fuse\Setup\Theme\ImageSize objects.
         */
        public function registerImageSizes () {
            $sizes = apply_filters ('fuse_image_sizes', array (
                // new ImageSize ('example_size', 400, 300, true)
            ));
            
            foreach ($sizes as $size) {
                add_image_size ($size->alias, $size->width, $size->height, $size->crop);
            } // foreach ()
        } // registerImageSizes ()
        
        
        
        
        /**
         *  Register the shortcodes.
         */
        public function registerShortcodes () {
            $shortcodes = apply_filters ('fuse_register_shortcodes', array (
                new Shortcode\ContentBlock (),
                new Shortcode\ContentColumn ()
            ));
            
            foreach ($shortcodes as $shortcode) {
                add_shortcode ($shortcode->getShortcode (), array ($shortcode, 'render'));
            } // foreach ()
        } // registerShortcodes ()
        
        
        
        
        /**
         *  Add our layout body classes.
         */
        public function bodyClass ($classes) {
            $fuse = \Fuse\Fuse::getInstance ();
            $layout = $fuse->layout;
            
            $parts = get_post_meta ($layout->getLayout (), 'fuse_layout_parts', true);

            $col_count = 0;

            foreach (array ('left_1', 'left_2', 'right_1', 'right_2') as $col) {
                if ($parts [$col] === true) {
                    $col_count++;
                } // if ()
            } // foreach ()

            switch ($col_count) {
                case 4:
                    $classes [] ='fuse-layout-five-col';
                    break;
                case 3:
                    $classes [] ='fuse-layout-four-col';
                    break;
                case 2:
                    $classes [] ='fuse-layout-three-col';
                    break;
                case 1:
                    $classes [] ='fuse-layout-two-col';
                    break;
                case 0:
                    $classes [] ='fuse-layout-single-col';
            } // switch ()

            if ($parts ['left_1'] == 1 && $parts ['left_2'] == 1) {
                $classes [] = 'fuse-layout-double-left-col';
            } // if ()
            elseif ($parts ['left_1'] == 1) {
                $classes [] = 'fuse-layout-single-left-col';
            } // elseif ()

            if ($parts ['right_1'] == 1 && $parts ['right_2'] == 1) {
                $classes [] = 'fuse-layout-double-right-col';
            } // if ()
            elseif ($parts ['right_1'] == 1) {
                $classes [] = 'fuse-layout-single-right-col';
            } // elseif ()
            
            /**
             *  Set the layout class.
             */
            $post = get_post ($layout->getLayout ());
            $classes [] = 'fuse-layout-template-'.$post->post_name;

            /**
             *  Header
             */
            if ($parts ['header'] == 1) {
                $classes [] = 'with-header';
            } // if ()
            else {
                $classes [] = 'without-header';
            } // else

            /**
             *  Footer
             */
            if ($parts ['footer'] == 1) {
                $classes [] = 'with-footer';
            } // if ()
            else {
                $classes [] = 'without-footer';
            } // else

            /**
             *  Sidebars
             */
            if ($parts ['left_1'] == 1) {
                $classes [] = 'with-left-sidebar';

                if ($parts ['left_2'] == 1) {
                    $classes [] = 'double-left-sidebar';
                } // if ()
            } // if ()

            if ($parts ['right_1'] == 1) {
                $classes [] = 'with-right-sidebar';

                if ($parts ['right_2'] == 1) {
                    $classes [] = 'double-right-sidebar';
                } // if ()
            } // if ()
            
            return $classes;
        } // bodyClasses ()
        
        
        
        
        /**
         *  Remove extra <p> tags from shortcodes. WordPress adds these in
         *  and there's no fix in core for it.
         *
         *  @param string $content The content.
         *
         *  @return string The formatted content.
         */
        public function removeShortcodeP ($content) {
            $array = array (
				'<p>[' => '[', 
				']</p>' => ']', 
				']<br />' => ']'
			);
			
			$content = strtr ($content, $array);
			
			if (substr ($content, 0, 4) == '</p>') {
				$content = substr ($content, 4);
			} // if ()
			
			if (substr ($content, -3, 3) == '<p>') {
				$content = substr ($content, 0, strlen ($content) - 3);
			} // if ()
			
			return trim ($content);
        } // removeShortcodeP ()
        
    } // class Theme