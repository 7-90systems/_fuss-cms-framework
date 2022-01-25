<?php
    /**
     *  @package fuse-cms-framework
     *
     *  Set up our JavaScript enqueues.
     *
     *  @filter fuse_enqueue_javascript_folder_locations
     */
    
    namespace Fuse\Setup\Theme\Enqueue;
    
    use Fuse\Setup\Theme\Enqueue;
    
    
    class JavaScript extends Enqueue {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('.js');
        } // __construct ()
        
        
        
        
        /**
         *  Set the folders that we will search in.
         */
        protected function _setFolders () {
            $folders = array (
                array (
                    'path' => get_template_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript',
                    'url' => untrailingslashit (get_template_directory_uri ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript')
                )
            );
            
            if (is_child_theme ()) {
                $folders [] = array (
                    'path' => get_stylesheet_directory ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript',
                    'url' => untrailingslashit (get_stylesheet_directory_uri ().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'javascript')
                );
            } // if ()
            
            $this->_base_folder_uri = apply_filters ('fuse_enqueue_javascript_folder_locations', $folders);
        } // _setFolders ()
        
        
        
        
        /**
         *  Enqueue our JavaScript files.
         */
        protected function _enqueue () {
            foreach ($this->_files as $alias => $file) {
                wp_register_script ($alias, $file ['file'], $deps);
            } // foreach ()
        } // _enqueue ()
        
    } // class JavaScript