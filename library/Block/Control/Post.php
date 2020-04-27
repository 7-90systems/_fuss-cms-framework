<?php
    /**
     *  @package fusecms
     *
     *  Set up the post control.
     */
    
    namespace Fuse\Block\Control;
    
    use Fuse\Block\Control;
    use Fuse\Block\Control\Setting;
    
    
    class Post extends Control {
    
        /**
         * Post constructor.
         */
        public function __construct () {
            $this->type = 'object';
            
            parent::__construct ('post', __ ('Post', 'fuse'));
        } // __construct ()
    
    
    
    
        /**
         *  Register settings.
         */
        protected function _registerSettings () {
            $this->settings [] = new Setting ($this->settings_config ['location']);
            $this->settings [] = new Setting ($this->settings_config ['width']);
            $this->settings [] = new Setting ($this->settings_config ['help']);
            $this->settings [] = new Setting (array (
                'name' => 'post_type_rest_slug',
                'label' => __ ('Post Type', 'fuse'),
                'type' => 'post_type_rest_slug',
                'default' => 'posts',
                'sanitise' => array ($this, 'sanitisePostTypeRestSlug')
            ));
        } // _registerSettings ()
    
    
    
    
        /**
         *  Render a <select> of public post types.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsPost_type_rest_slug ($setting, $name, $id) {
            $this->renderSelect ($setting, $name, $id, $this->getPostTypeRestSlugs ());
        } // renderSettingsPost_type_rest_slug ()
    
        /**
         * Gets the REST slugs of public post types, other than 'attachment'.
         *
         * @return array {
         *     An associative array of the post type REST slugs.
         *
         *     @type string $rest_slug The REST slug of the post type.
         *     @type string $name      The name of the post type.n
         * }
         */
        public function getPostTypeRestSlugs () {
            $post_type_rest_slugs = array ();
            
            foreach (get_post_types (array ('public' => true)) as $post_type) {
                $post_type_object = get_post_type_object ($post_type);
                
                if ($post_type != 'attahcment' && $post_type_object && empty ($post_type_object->show_in_rest) === false) {
                    $rest_slug = !empty ($post_type_object->rest_base) ? $post_type_object->rest_base : $post_type;
                    $labels = get_post_type_labels ($post_type_object);
                    $post_type_name = isset ($labels->name) ? $labels->name : $post_type;
                    $post_type_rest_slugs [$rest_slug] = $post_type_name;
                } // if ()
            } // foreach ()
            
            return $post_type_rest_slugs;
        } // getPostTypeRestSlugs ()
    
        /**
         *  Sanitise the post type REST slug, to ensure that it's a public post
         *  type.
         *
         *  This expects the rest_base of the post type, as it's easier to pass
         *  that to apiFetch in the Post control. So this iterates through the
         *  public post types, to find if one has the rest_base equal to $value.
         *
         *  @param string $value The rest_base of the post type to sanitize.
         *
         *  @return string|null The sanitized rest_base of the post type, or null.
         */
        public function sanitisePostTypeRestSlug ($value) {
            $new_value = NULL;
            
            if (array_key_exists ($value, $this->getPostTypeRestSlugs ())) {
                $new_value = $value;
            } // if ()
            
            return $new_value;
        } // sanitisePostTypeRestSlug ()
    
        /**
         *  Validates the value to be made available to the front-end template.
         *
         *  @param mixed $value The value to either make available as a variable
         *  or echoed on the front-end template.
         *  @param bool  $echo  Whether this will be echoed.
         *
         *  @return string|WP_Post|null $value The value to be made available or
         *  echoed on the front-end template.
         */
        public function validate ($value, $echo) {
            $post = isset ($value ['id']) ? get_post ($value ['id']) : NULL;
            
            if ($echo === true) {
                $post = get_the_title ($post);
            } // if 9)
            
            return $post;
        } // validate ()
        
    } // class Post