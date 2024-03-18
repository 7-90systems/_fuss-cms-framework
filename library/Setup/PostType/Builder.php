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
            
            add_action ('add_meta_boxes', array ($this, 'addMetaBoxes'));
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
                
                $slug = get_post_meta ($posttype->ID, 'fuse_posttype_builder_slug', true);
                
                if (strlen ($slug) > 0) {
                    register_post_type ($slug, $args);
                } // if ()
            } // foreach ()
        } // registerPostTypes ()
        
        
        
        
        /**
         *  Add our meta boxes.
         */
        public function addMetaBoxes () {
            $posttypes = get_posts (array (
                'numberposts' => -1,
                'post_type' => 'fuse_posttype'
            ));
            
            foreach ($posttypes as $posttype) {
                $metaboxes = json_decode (get_post_meta ($posttype->ID, 'fuse_builder_metaboxes', true));
            
                if (is_array ($metaboxes) === true) {
                    foreach ($metaboxes as $metabox) {
                        $section = $metabox->section;
                        $name = $metabox->name;
                        $slug = get_post_meta ($posttype->ID, 'fuse_posttype_builder_slug', true);
                        
                        if ($section != 'side') {
                            $section = 'normal';
                        } // if ()
                        
                        add_meta_box ('fuse_builder_metabox_'.uniqid (), $name, array ($this, 'metabox'), $slug, $section, 'default', array (
                            'posttype' => $posttype,
                            'name' => $name
                        ));
                    } // foreach ()
                } // if ()
            } // foreach ()
        } // addMetaBoxes ()
        
        /**
         *  Set up our meta boxes!
         */
        public function metabox ($post, $args = array ()) {
            $args = $args ['args'];
            $name = $args ['name'];
            $posttype = $args ['posttype'];
            $metaboxes = json_decode (get_post_meta ($posttype->ID, 'fuse_builder_metaboxes', true));
            
            foreach ($metaboxes as $metabox) {
                if ($metabox->name == $name) {
                    $fields = $metabox->fields;
                    ?>
                        <?php if (count ($fields) > 0): ?>
                        
                            <table class="form-table">
                                
                                <?php foreach ($fields as $field): ?>
                                
                                    <tr>
                                        <th><?php echo $field->name; ?></th>
                                        <td>
                                            <?php
                                                $this->_showField ($post->ID, $field);
                                            ?>
                                        </td>
                                    </tr>
                                
                                <?php endforeach; ?>
                                
                            </table>
                        
                        <?php else: ?>
                        
                            <p><?php _e ('There are no settings for this post type', 'fuse'); ?></p>
                        
                        <?php endif; ?>
                    <?php
                } // if ()
            } // foreach ()
        } // metabox ()
        
        
        
        
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
        
        
        
        
        /**
         *  Show a metabox field.
         */
        protected function _showField ($post_id, $field) {
            switch ($field->type) {
                case 'number':
                    $this->_showNumberField ($post_id, $field);
                    break;
                case 'select':
                    $this->_showSelectField ($post_id, $field);
                    break;
                default:
                    $this->_showTextField ($post_id, $field);
            } // switch ()
        } // _showField ()
        
        /**
         *  Show a text field.
         */
        protected function _showTextField ($post_id, $field) {
            ?>
                <input type="<?php esc_attr_e ($field->type); ?>" name="<?php esc_attr_e ($field->key); ?>" value="<?php esc_attr_e (get_post_meta ($post_id, $field->key, true)); ?>" class="large-text" />
            <?php
        } // _showTextField ()
        
        /**
         *  Show a number field.
         */
        public function _showNumberField ($post_id, $field) {
            $atts = array ();
            $att_keys = array ('min', 'max', 'step');
            
            foreach ((array) $field->settings as $key => $val) {
                if (in_array ($key, $att_keys) === true) {
                    $atts [] = $key.'="'.$val.'"';
                } // if ()
            } // foreach ()
            ?>
                <input type="number" name="<?php esc_attr_e ($field->key); ?>" value="<?php esc_attr_e (get_post_meta ($post_id, $field->key, true)); ?>" class="regular-text" <?php echo implode (' ', $atts); ?> />
            <?php
        } // _showNumberField ()
        
        /**
         *  Show a select field.
         */
        protected function _showSelectField ($post_id, $field) {
            $options = explode ("\n", $field->settings->options);
            $selected = get_post_meta ($post_id, $field->key, true);
            $multiple = $field->settings->selecttype == 'multi' ? ' multiple' : '';
            ?>
                <select name="<?php esc_attr_e ($field->key); ?>"<?php echo $multiple; ?>>
                    <?php if ($multiple == ''): ?>
                        <option value="">&nbsp;</option>
                    <?php endif; ?>
                    <?php
                        foreach ($options as $option) {
                            $option = explode ('|', $option);
                            
                            if (count ($option) == 1) {
                                $option [1] = $option [0];
                            } // if ()
                            
                            echo '    <option value="'.esc_attr (trim ($option [0])).'"'.selected ($selected, trim ($option [0])).'>'.trim ($option [1]).'</option>'.PHP_EOL;
                        } // foreach ()
                    ?>
                </select>
            <?php
        } // _showSelectField ()
        
    } // class Builder