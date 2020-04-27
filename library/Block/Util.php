<?php
    /**
     *  Set up our block utility class.
     *
     *  @filter fuse_block_template_path Filter the block templates paths.
     *  @filter fuse_block_icons Filter the block icons.
     *  @filter fuse_block_allowed_svg_tags Filter the allowed SVG tags
     *  @filter fuse_block_data Filter block data
     *  @filter fuse_block_data_{KEY} Filter block data for the specified key.
     */
    
    namespace Fuse\Block;
    
    
    class Util {
        
        /**
         *  @var array This array holds the block set up values.
         */
        static public $data = array ();
        
        
        
        
        /**
         *  Block direct instantiation.
         */
        private function __construct () {
            // Nothing to do here!
        } // __construct ()
        
        
        
        
        /**
         *  Gets an array of possible template locations.
         *
         *  @param string $name The name of the block (slug as defined in UI).
         *  @param string $type The type of template to load. Typically block or
         *  preview.
         *
         *  @return array
         */
        static public function getTemplateLocations ($name, $type = 'block') {
            return array (
                'templates/blocks/'.$name.'/'.$type.'.php',
                'templates/blocks/'.$type.'-'.$name.'.php',
                'templates/blocks/'.$name.'.php'
            );
        } // getTemplateLocations ()
        
        
            
            
        /**
         *  Locates templates.
         *
         *  Works similar to `locate_template`, but allows specifying a path
         *  outside of themes and allows to be called when STYLESHEET_PATH has
         *  not been set yet. Handy for async.
         *
         *  @param string|array $template_names Templates to locate.
         *  @param string       $path           (Optional) Path to locate the templates first.
         *  @param bool         $single         `true` - Returns only the first found item. Like standard `locate_template`
         *                                      `false` - Returns all found templates.
         *
         *  @return string|array
         */
        static public function locateTemplate ($template_names, $path = '', $single = true) {
            if (is_array ($template_names) === false) {
                $template_names = array ($template_names);
            } // if ()
            
            $path = apply_filters ('fuse_block_template_path', $path, $template_names);
    
            $stylesheet_path = get_template_directory ();
            $template_path   = get_stylesheet_directory ();
    
            $located = array ();
    
            foreach ($template_names as $template_name) {
                if ($template_name) {
                    if (!empty ($path) && file_exists (trailingslashit ($path).$template_name)) {
                        $located [] = trailingslashit ($path).$template_name;
                    } // if ()
        
                    if (file_exists (trailingslashit ($template_path).$template_name)) {
                        $located [] = trailingslashit ($template_path).$template_name;
                    } // if ()
        
                    if (file_exists (trailingslashit ($stylesheet_path).$template_name)) {
                        $located [] = trailingslashit ($stylesheet_path).$template_name;
                    } // if ()
        
                    if (file_exists (ABSPATH.WPINC.'/theme-compat/'.$template_name)) {
                        $located [] = ABSPATH.WPINC.'/theme-compat/'.$template_name;
                    } // if ()
                } // if ()
            } // foreach ()
    
            // Remove duplicates and re-index array.
            $located = array_values (array_unique ($located));
    
            if ($single) {
                $located = array_shift ($located);
            } // if ()
    
            return $located;
        } // locateTemplate ()
        
        
        
        
        /**
         *  Provides a list of all available block icons.
         *
         *  To include additional icons in this list, use the block_lab_icons
         *  filter, and add a new svg string to the array, using a unique key.
         *  For example:
         *      $icons['foo'] = '<svg>…</svg>';
         *
         *  @return array
         */
        static public function getIcons () {
            $json = file_get_contents (FUSE_BASE_URL.'/assets/javascript/block/icons.json');
            $icons = json_decode ($json, true);
    
            return apply_filters ('fuse_block_icons', $icons);
        } // getIcons ()
        
        /**
         * Provides a list of allowed tags to be used by an <svg>.
         *
         * @return array
         */
        static public function allowedSvgTags() {
            $allowed_tags = array (
                'svg'    => array (
                    'xmlns'   => true,
                    'width'   => true,
                    'height'  => true,
                    'viewbox' => true,
                ),
                'g'      => array ('fill' => true),
                'title'  => array ('title' => true),
                'path'   => array (
                    'd'       => true,
                    'fill'    => true,
                    'opacity' => true,
                ),
                'circle' => array (
                    'cx'   => true,
                    'cy'   => true,
                    'r'    => true,
                    'fill' => true,
                ),
            );
    
            return apply_filters ('fuse_block_allowed_svg_tags', $allowed_tags);
        } // allowedSvgTags ()
        
        
        
        
        /**
         *  Gets an array of possible stylesheet locations.
         *
         *  @param string $name The name of the block (slug as defined in UI).
         *   @param string $type The type of template to load. Typically block
         *   or preview.
         *
         *   @return array
         */
        static public function getStylesheetLocations ($name, $type = 'block') {
            return array (
                'assets/css/blocks/'.$name.'/'.$type.'.css',
                'assets/css/blocks/'.$type.'-'.$name.'.css'
            );
        } // getStylesheetLocations ()
        
        
        
        
        /**
         *  Get a relative URL from a path.
         *
         *  @param string $path The absolute path to a file.
         *
         *  @return string
         */
        static public function getUrlFromPath ($path) {
            $abspath = ABSPATH;
    
            // Workaround for weird hosting situations.
            if (trailingslashit (ABSPATH).'wp-content' !== WP_CONTENT_DIR && isset ($_SERVER ['DOCUMENT_ROOT'])) {
                $abspath = sanitize_text_field (wp_unslash ($_SERVER ['DOCUMENT_ROOT']));
            } // if ()
    
            $stylesheet_url = str_replace (untrailingslashit ($abspath), '', $path);
    
            return $stylesheet_url;
        } // getUrlFromPath ()
        
        
        
        
        /**
         *  Retrieve data from the data store.
         *
         *  @param string $key The data key to retrieve.
         *
         *  @return mixed
         */
        static public function getData ($key) {
            $data = false;
            
            /**
             *  TODO: Figure out where the data is coming from...  :(
             */
            
            /*
            if (isset ($this->data [$key])) {
                $data = $this->data [$key];
            } // if ()
            */
    
            /**
             * Filters the data that gets returned.
             *
             * @param mixed  $data The data from the Loader's data store.
             * @param string $key  The key for the data being retreived.
             */
            $data = apply_filters ('fuse_block_data', $data, $key);
    
            /**
             * Filters the data that gets returned, specifically for a single key.
             *
             * @param mixed  $data The data from the Loader's data store.
             */
            $data = apply_filters ('fuse_block_data_'.$key, $data);
    
            return $data;
        } // getData ()
        
    } // class Util