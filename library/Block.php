<?php
    /**
     *  @package fusecms
     *
     *  This is a Block.
     */
    
    namespace Fuse;
    
    use Fuse\Block\Field;
    
    
    class Block {
    
        /**
         *  @var string Block name (slug).
         */
        public $name = '';
    
        /**
         *  @var string Block title.
         */
        public $title = '';
    
        /**
         * @var array Exclude the block in these post types.
         */
        public $excluded = array ();
    
        /**
         * @var string Icon.
         */
        public $icon = '';
    
        /**
         * @var array Category. An array containing the keys slug, title, and icon.
         */
        public $category = array (
            'slug'  => '',
            'title' => '',
            'icon'  => ''
        );
    
        /**
         * @var array Block keywords.
         */
        public $keywords = array ();
    
        /**
         * @var array Block fields.
         */
        public $fields = array ();
    
    
    
    
        /**
         *  Object consturctor
         *
         *  @param int|bool $post_id Post ID.
         *  
         */
        public function __construct ($post_id = false) {
            if ($post_id !== false) {
                $post = get_post ($post_id);
        
                if ($post instanceof \WP_Post) {
                    $this->name = $post->post_name;
                    $this->fromJson ($post->post_content);
                } // if ()
            } // if ()
        } // __construct ()
        
        
        
    
        /**
         *  Construct the Block from a JSON blob.
         *
         *  @param string $json JSON blob.
         */
        public function fromJson ($json) {
            $json = json_decode ($json, true);
    
            if (isset ($json ['fuse/'.$this->name])) {
                $config = $json ['fuse/'.$this->name];
        
                $this->fromArray ($config);
            } // if ()
        } // fromJson ()
    
        /**
         *  Construct the Block from a config array.
         *
         *  @param array $config An array containing field parameters.
         */
        public function fromArray ($config) {
            if (isset ($config ['name'])) {
                $this->name = $config ['name'];
            } // if ()
    
            if (isset ($config ['title'])) {
                $this->title = $config ['title'];
            } // if ()
    
            if (isset ($config ['excluded'])) {
                $this->excluded = $config ['excluded'];
            } // if ()
    
            if (isset ($config ['icon'])) {
                $this->icon = $config ['icon'];
            } // if ()
    
            if (isset ($config ['category'])) {
                $this->category = $config ['category'];
                
                if (!is_array ($this->category)) {
                    $this->category = $this->_getCategoryArrayFromSlug ($this->category);
                } // if ()
            } // if ()
    
            if (isset ($config ['keywords'])) {
                $this->keywords = $config ['keywords'];
            } // if ()
    
            if (isset ($config ['fields'])) {
                foreach ( $config ['fields'] as $key => $field) {
                    $this->fields [$key] = new Field ($field);
                } // foreach ()
            } // if ()
        } // fromArray ()
        
        
        
    
        /**
         *  Get the Block as a JSON blob.
         *
         *  @return string
         */
        public function toJson () {
            $config ['name'] = $this->name;
            $config ['title'] = $this->title;
            $config ['excluded'] = $this->excluded;
            $config ['icon'] = $this->icon;
            $config ['category'] = $this->category;
            $config ['keywords'] = $this->keywords;
            $config ['fields'] = array ();
            
            foreach ($this->fields as $key => $field) {
                $config ['fields'][$key] = $field->toArray ();
            } // foreach ()
    
            return wp_json_encode (array ('fuse/'.$this->name => $config), JSON_UNESCAPED_UNICODE);
        } // toJson ()
        
        
        
    
        /**
         *  This is a backwards compatibility fix.
         *
         *  Block categories used to be saved as strings, but were always
         *  included in the default list of categories, so we can find them.
         *
         *  It's not possible to use get_block_categories() here, as Block's are
         *  sometimes instantiated before that function is available.
         *
         *  @param string $slug The category slug to find.
         *
         *  @return array
         */
        protected function _getCategoryArrayFromSlug ($slug) {
            return array (
                'slug' => $slug,
                'title' => ucwords ($slug, '-'),
                'icon' => NULL
            );
        } // _getCategoryArrayFromSlug ()
    }