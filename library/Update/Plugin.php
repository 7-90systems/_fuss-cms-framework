<?php
    /**
     *  @package fuse-cms
     *
     *  This class takes care of our plugin updates for our Fuse plugins.
     */
    
    namespace Fuse\Update;
    
    use WP_Error;
    use Fuse\Traits\Update;
    
    
    class Plugin {
        
        use Update;
        
        
        
        
        /**
         *  @var array Our plugins list. Don't access this list directly. Use the getPlugins() function in this class.
         */
        private $_plugins;
        
        
        
        
        /**
         *  Object constructor
         */
        public function __construct () {
            // add_action ('init', array ($this, 'getPlugins'));
            
            add_filter ('pre_set_site_transient_update_plugins', array ($this, 'checkForPluginUpdate'));
            /**
             *  TODO: Leaving this here for now. It may be needed, but at the moment it doesn't look like it.
             */
            add_filter ('plugins_api', array ($this, 'pluginApiCall'), 10, 3);
        } // __construct ()
        
        
        
        
        /**
         *  Check for any plugin updates
         */
        public function checkForPluginUpdate ($checked_data) {
            global $api_url, $plugin_slug, $wp_version;
            
            if (empty ($checked_data->checked) === false) {
                foreach ($this->getPlugins () as $plugin_file => $update_server) {
                    $version = array_key_exists ($plugin_file, $checked_data->checked) ? $checked_data->checked [$plugin_file] : '0';
                    $args = array (
                        'slug' => $plugin_file,
                        'version' => $version,
                    );
                    $request_string = array (
                        'body' => array (
                            'action' => 'basic_check', 
                            'request' => serialize ($args),
                            'api-key' => md5 (get_bloginfo ('url'))
                        ),
                        'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo ('url'),
                        'timeout' => 60
                    );
                    
                    // Start checking for an update
error_log ("Plugin update: '".$this->_getServerUrl ($update_server)."'");
                    $raw_response = wp_remote_post ($this->_getServerUrl ($update_server), $request_string);
                    $response = NULL;
                    
                    if (!is_wp_error ($raw_response) && ($raw_response ['response']['code'] == 200)) {
error_log ("  - Got good response");
                        $response = maybe_unserialize ($raw_response ['body']);
                    } // if ()
elseif (is_wp_error ($raw_response)) {
error_log ("  - WP Error :( - Error: '".$raw_response->get_error_message ()."'");
}
else {
error_log ("  - Unknown error :( - Error: '".is_wp_error ($raw_response)."'");
}
                    
                    if (is_object ($response) && !empty ($response)) {
                        $checked_data->response [$plugin_file] = $response;
                    } // if ()
                } // foreach ()
            } // if ()
            
            return $checked_data;
        } // check_for_plugin_update()
        
        /**
         *  Perform the plugin API call
         */
        public function pluginApiCall ($def, $action, $args) {
// \Fuse\Debug::dump ($args, 'Args', true);
            global $wp_version;
            
            $res = false;
            
            foreach ($this->getPlugins () as $slug => $update_server) {
                if (property_exists ($args, 'slug') && $slug == $args->slug) {
                    $plugin_data = get_plugin_data (trailingslashit (WP_PLUGIN_DIR).$slug);
                    $args->version = $plugin_data ['Version'];
                    
                    $request_string = array (
                        'body' => array (
                            'action' => $action, 
                            'request' => serialize ($args),
                            'api-key' => md5 (get_bloginfo ('url'))
                        ),
                        'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo ('url')
                    );
                    
                    $request = wp_remote_post ($this->_getServerUrl ($update_server), $request_string);
                    
                    if (is_wp_error ($request)) {
                        $res = new WP_Error ('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message ());
                    } // if ()
                    else {
                        $res = unserialize ($request ['body']);
                        
                        if ($res === false) {
                            $res = new WP_Error ('plugins_api_failed', __('An unknown error occurred'), $request ['body']);
                        } // if ()
                    } // else
                } // if ()
            } // foreach ()
            
            return $res;
        } // pluginApiCall ()
        
        
        
        
        /**
         *  Get the list of plugins.
         */
        public function getPlugins () {
            if (empty ($this->_plugins)) {
                $this->_plugins = array ();
                
                $plugins = get_plugins ();
                
                foreach ($plugins as $file => $data) {
                    $file_uri = trailingslashit (WP_PLUGIN_DIR).$file;
                    
                    if (file_exists ($file_uri)) {
                        $fh = fopen ($file_uri, 'r');
                        $has_fuse_update = false;
                        
                        while ($has_fuse_update === false && ($line = fgets ($fh, 8092)) !== false) {
                            $line = trim ($line, ' *');
                            
                            if (strtolower (substr ($line, 0, 19)) == 'fuse update server:') {
                                $this->_plugins [$file] = trim (substr ($line, 19));
                                $has_fuse_update = true;
                            } // if ()
                        } // while ()
                        
                        fclose ($fh);
                    } // if ()
                } // foreach
            } // if ()
            
            return $this->_plugins;
        } // getPlugins ()
        
    } // class Plugin