<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This class takes care of our plugin and theme updates. This is used for
     *  the Fuse CMS Framework and all of our other plugins and themes.
     */
    
    namespace Fuse;
    
    use WP_Error;
    
    
    class Update {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            // THEME updates
            add_filter ('pre_set_site_transient_update_themes', array ($this, 'checkForThemeUpdates'));
            add_filter ('themes_api', array ($this, 'themeApiCall', 10, 3));
            
            // PLUGIN updates
            // add_filter ('pre_set_site_transient_update_plugins', array ($this, 'checkForPluginUpdates'));
            // add_filter ('plugins_api', array ($this, 'pluginApiCall', 10, 3));
        } // __construct ()
        
        
        
        
        /**
         *  Check for theme updates
         */
        public function checkForThemeUpdates ($checked_data) {
            global $wp_version;
// echo "<p>WordPress Version: '".$wp_version."'</p>";
            $themes = $this->_getThemesList ();
            
            foreach ($themes as $theme_base => $update_server_url) {
// echo "<p>Checking theme '".$theme_base."' at server '".$update_server_url."'</p>";
                $theme = wp_get_theme ($theme_base);
            
                $request = array (
                    'slug' => $theme_base,
                    'version' => $theme->get ('Version')
                );

                // Start checking for an update
                $send_for_check = array (
                    'body' => array (
                        'action' => 'theme_update', 
                        'request' => serialize ($request),
                        'api-key' => md5 (get_bloginfo ('url')),
                        'user-agent' => 'fuse/'.$wp_version.'; '.get_bloginfo ('url')
                    )
                );
                
                $raw_response = wp_remote_post ($update_server_url.'/wp-json/updateserver/v1/themeupdate', $send_for_check);
// \Fuse\Debug::dump ($raw_response, 'Raw response from "'.$update_server_url.'/wp-json/updateserver/v1/theme"');

                $response = NULL;
                
                if (!is_wp_error ($raw_response) && ($raw_response ['response']['code'] == 200)) {
                    $response = maybe_unserialize ($raw_response ['body']);
                } // if ()
// \Fuse\Debug::dump ($response, 'Response');
            
                // Feed the update data into WP updater
                if (!empty ($response) && is_array ($response)) {
                    $checked_data->response [$theme_base] = $response;
                } // if ()
            } // foreach ()
// \Fuse\Debug::dump ($checked_data, 'Checked data', true);
            return $checked_data;
        } // checkForThemeUpdates ()
        
        /**
         *  Call the theme API.
         */
       public function themeApiCall ($def, $action, $args) {
            global $theme_base;
            global $api_url;
            global $theme_version;
            
            if ($args->slug != $theme_base) {
                return false;
            } // if ()
            
            // Get the current version
            $args->version = $theme_version;
            $request_string = prepare_request ($action, $args);
            $request = wp_remote_post ($api_url, $request_string);
        
            if (is_wp_error ($request)) {
                $res = new WP_Error ('themes_api_failed', __ ('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'fuse'), $request->get_error_message ());
            } // if ()
            else {
                $res = unserialize ($request ['body']);
                
                if ($res === false) {
                    $res = new WP_Error ('themes_api_failed', __ ('An unknown error occurred', 'fuse'), $request ['body']);
                } // if ()
            } // else
            
            return $res;
        } // themeApiCall ()
        
        
        
        
        /**
         *  Check for plugin updates.
         */
        public function checkForPluginUpdates ($checked_data) {
            $plugins = $this->_getPluginsList ();
            
            global $api_url;
            global $plugin_slug;
            global $wp_version;
            
            //Comment out these two lines during testing.
            if (empty ($checked_data->checked)) {
                /**
                 *  TOOD: REmove this when testing is done...
                 */
                // return $checked_data;
            } // if ()
            
            $args = array (
                'slug' => $plugin_slug,
                'version' => $checked_data->checked [$plugin_slug.'/'.$plugin_slug.'.php']
            );
            $request_string = array (
                'body' => array (
                    'action' => 'basic_check', 
                    'request' => serialize ($args),
                    'api-key' => md5 (get_bloginfo ('url')),
                    'user-agent' => 'fuse/'.$wp_version.'; '.get_bloginfo ('url')
                )
            );
            
            // Start checking for an update
            $raw_response = wp_remote_post ($api_url, $request_string);
            
            if (!is_wp_error ($raw_response) && ($raw_response ['response']['code'] == 200)) {
                $response = unserialize ($raw_response ['body']);
            } // if ()
            
            if (is_object($response) && !empty($response)) {
                $checked_data->response [$plugin_slug.'/'.$plugin_slug.'.php'] = $response;
            } // if ()
            
            return $checked_data;
        } // checkForPluginUpdates ()
        
        /**
         *  Set up the plugin API call.
         */
        public function pluginApiCall ($def, $action, $args) {
            global $plugin_slug;
            global $api_url;
            global $wp_version;
            
            if (!isset ($args->slug) || ($args->slug != $plugin_slug)) {
               return false;
            } // if ()
            
            // Get the current version
            $plugin_info = get_site_transient ('update_plugins');
            $current_version = $plugin_info->checked [$plugin_slug.'/'.$plugin_slug .'.php'];
            $args->version = $current_version;
            
            $request_string = array (
                'body' => array(
                    'action' => $action, 
                    'request' => serialize ($args),
                    'api-key' => md5 (get_bloginfo ('url'))
                ),
                'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo ('url')
            );
            
            $request = wp_remote_post ($api_url, $request_string);
            
            if (is_wp_error ($request)) {
                $res = new WP_Error ('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'fuse'), $request->get_error_message ());
            } // if ()
            else {
                $res = unserialize ($request ['body']);
                
                if ($res === false){
                    $res = new WP_Error ('plugins_api_failed', __ ('An unknown error occurred', 'fuse'), $request ['body']);
                } // if ()
           } // else
           
           return $res;
        } // pluginApiCall ()
        
        
        
        
        /**
         *  Get a list of all plugins that have the Fuse updates set up.
         *
         *  @return array The list of plugins.
         */
        protected function _getPluginsList () {
            $plugins_list = array ();
            
            foreach (get_plugins () as $path => $data) {
                $data = get_file_data (trailingslashit (WP_PLUGIN_DIR).$path, array ('updateserver' => 'Fuse Update Server'), 'plugin');

                if (strlen ($data ['updateserver']) > 0) {
                    $plugins_list [$path] = $data ['updateserver'];
                } // if ()
            } // foreach ()
// \Fuse\Debug::dump ($plugins_list, 'Plugins list', true);
// \Fuse\Debug::dump ($plugins, 'Plugins', true);
            
            
            return $plugins_list;
        } // _getPluginsList ()
        
        /**
         *  Get the list of themes that have the Fuse updates set up.
         *
         *  @return array The list of current themes.
         */
        protected function _getThemesList () {
            $themes_list = array ();
            
            $themes = wp_get_themes ();
            
            foreach (wp_get_themes () as $theme) {
                $style_file = trailingslashit (trailingslashit ($theme->get_theme_root ()).$theme->get_stylesheet ()).'style.css';
                $data = get_file_data ($style_file, array ('updateserver' => 'Fuse Update Server'), 'theme');
                
                if (strlen ($data ['updateserver']) > 0) {
                    $themes_list [$theme->get_stylesheet ()] = $data ['updateserver'];
                } // if ()
                
// \Fuse\Debug::dump ($data, 'Data for '.$style_file);
            } // foreach ()
// \Fuse\Debug::dump ($themes_list, 'Themes', true);
            
            return $themes_list;
        } // _getThemesList ()
        
    } // class Update