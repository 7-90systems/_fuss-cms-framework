<?php
    /**
     *  This class sets up our custom post type builder post types.
     */
    
    namespace Fuse\Setup\PostType;
    
    use Fuse\Traits\Singleton;
    use Fuse\Model;
    
    
    class Builder {
        
        use Singleton;
        
        
        
        
        /**
         *  Initialise our class.
         */
        protected function _init () {
            add_action ('init', array ($this, 'registerPostTypes'));
        } // _init ()
        
        
        
        
        /**
         *  Register our post types
         */
        public function registerPostTypes () {
            $posttypes = get_posts (array (
                'numberposts' => -1,
                'post_type' => 'fuse_posttype'
            ));
            
            foreach ($posttypes as $posttype) {
                $model = new Model\PostType\Builder ($posttype);
                
                $args = $this->_formatSettings ($model->getSettings ());
                $args ['labels'] = $this->_formatLabels ($model->getLabels ());
                
                $result = register_post_type (get_post_meta ($posttype->ID, 'fuse_posttype_builder_slug', true), $args);
// \Fuse\Debug::dump ($result, 'Register result', true);
            } // foreach ()
        } // registerPostTypes ()
        
        
        
        
        /**
         *  Format our settings
         */
        protected function _formatSettings ($settings) {
            $formatted = array ();
            
            foreach ($settings as $key => $setting) {
                switch ($setting ['type']) {
                    case 'toggle':
                        if ($setting ['value'] == 'yes') {
                            $value = true;
                        } // if ()
                        elseif ($setting ['value'] == 'no') {
                            $value = false;
                        } // elseif ()
                        else {
                            $value = NULL;
                        } // else
                        
                        break;
                    case 'options':
                        $value = $setting ['value'];
                        break;
                    default:
                        if (strlen ($setting ['value']) > 0) {
                            $value = $setting ['value'];
                        } // if ()
                        else {
                            $value = NULL;
                        } // else
                } // switch ()
                
                if (is_null ($value) === false) {
                    $formatted [$key] = $value;
                } // if ()
            } // foreach ()
            
            return $formatted;
        } // _formatSettings ()
        
        /**
         *  Format our labels.
         */
        protected function _formatLabels ($labels) {
            $formatted = array ();
            
            foreach ($labels as $key  => $label) {
                $value = $label ['value'];
                
                if (strlen ($value) > 0) {
                    $formatted [$key] = $label ['value'];
                } // if ()
            } // foreach ()
            
            return $formatted;
        } // labels ()
        
    } // class Builder