<?php
    /**
     *  @package fusecms
     *
     *  This is our post tyep builder class. We use this to allow non-technical
     *  users to create new post types quickly and easily in the admin area.
     */
    
    namespace Fuse\PostType;
    
    use Fuse\PostType;
    use Fuse\Model;
    use Fuse\Forms;
    
    
    class Builder extends PostType {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            parent::__construct ('fuse_posttype', __ ('Post Type', 'fuse'), __ ('Post Type Builder'), array (
                'public' => false,
                'publicly_queryable' => false,
                'rewrite' => false,
                'show_in_rest' => false,
                'show_in_menu' => 'fusesettings',
                'supports' => array (
                    'title'
                )
            ));
        } // __construct ()
        
        
        
        
        /**
         *  Set up our meta boxes.
         */
        public function addMetaBoxes () {
            add_meta_box ('fuse_posttype_builder_labels_meta', __ ('Post Type Labels', 'fuse'), array ($this, 'labelsMeta'), $this->getSlug (), 'normal', 'default');
            add_meta_box ('fuse_posttype_builder_settings_meta', __ ('Post Type Settings', 'fuse'), array ($this, 'settingsMeta'), $this->getSlug (), 'normal', 'default');
            add_meta_box ('fuse_postytpe_builder_metaboxes_meta', __ ('Meta Boxes', 'fuse'), array ($this, 'metaboxesMeta'), $this->getSlug (), 'normal', 'default');
        } // addMetaBoxes ()
        
        /**
         *  Add the settings meta box.
         */
        public function labelsMeta ($post) {
            $use_advanced = get_post_meta ($post->ID, 'fuse_posttype_builder_labels_advanced', true);
            $model = new Model\PostType\Builder ($post);
            
            if ($use_advanced != 'yes') {
                $use_advanced = 'no';
            } // if ()
            ?>
                <table class="form-table">
                    
                    <tr class=".fuse-forms-container">
                        <th><?php _e ('Label types', 'fuse'); ?></th>
                        <td>
                            <ul>
                               <li>
                                    <label>
                                        <input class="fuse-postytpe-builder-labels-advanced" type="radio" name="fuse_posttype_builder_labels_advanced" value="no"<?php checked ('no', $use_advanced); ?> />
                                        <?php _e ('Use standard labels', 'fuse'); ?>
                                    </label>
                               </li>
                               <li>
                                    <label>
                                        <input class="fuse-postytpe-builder-labels-advanced" type="radio" name="fuse_posttype_builder_labels_advanced" value="yes"<?php checked ('yes', $use_advanced); ?> />
                                        <?php _e ('Use advanced labels', 'fuse'); ?>
                                    </label>
                               </li>
                            </ul>
                        </td>
                    </tr>
                    
                    <?php foreach ($model->getLabels () as $key => $label): ?>

                        <tr class="label-<?php echo $label ['type']; ?>"<?php if ($label ['type'] == 'advanced' && $use_advanced == 'no') echo ' style="display: none;"'; ?>>
                            <th><?php echo $label ['label']; ?></th>
                            <td>
                                <input type="text" name="fuse_posttype_builder_labels[<?php esc_attr_e ($key); ?>]" value="<?php esc_attr_e ($label ['value']); ?>" class="regular-text"<?php if (array_key_exists ('placeholder', $label)) echo ' placeholder="'.esc_attr ($label ['placeholder']).'"'; ?> />
                            </td>
                        </tr>
                    
                    <?php endforeach; ?>

                </table>
                <script type="text/javascript">
                    jQuery (document).ready (function () {
                        let advanced_labels = jQuery ('tr.label-advanced');
                        
                        jQuery ('.fuse-postytpe-builder-labels-advanced').click (function () {
                            let val = jQuery (this).val ();
                            
                            if (val == 'yes') {
                                advanced_labels.show ();
                            } // if ()
                            else {
                                advanced_labels.hide ();
                            } // else
                        });
                    });
                </script>
            <?php
        } // labelsMeta ()
        
        /**
         *  Set up the settings meta box.
         */
        public function settingsMeta ($post) {
            $model = new Model\PostType\Builder ($post);
            ?>
                <table class="form-table">
                    <tr>
                        <th><?php _e ('Post type slug', 'fuse'); ?></th>
                        <td>
                            <input type="text" name="fuse_posttype_builder_slug" value="<?php esc_attr_e (get_post_meta ($post->ID, 'fuse_posttype_builder_slug', true)); ?>" maxlength="20" />
                            <p class="description"><?php _e ('Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores.', 'fuse'); ?></p>
                        </td>
                    </tr>
                    
                    <?php foreach ($model->getSettings () as $key => $setting): ?>
                    
                        <tr>
                            <th><?php echo $setting ['label']; ?></th>
                            <td>
                                <?php
                                    switch ($setting ['type']) {
                                        case 'toggle':
                                            $this->_toggleSetting ($key, $setting);
                                            break;
                                        case 'options':
                                            $this->_optionsSetting ($key, $setting);
                                            break;
                                        default:
                                            $this->_fieldSetting ($key, $setting);
                                    } // switch ()
                                ?>
                            </td>
                        </tr>
                    
                    <?php endforeach; ?>
                </table>
            <?php
        } // settingsMeta ()
        
        // Add the meta boxes meta box.
        public function metaboxesMeta ($post) {
            ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th style="width: 66.6%;"><?php _e ('Main Column', 'fuse'); ?></th>
                            <th style="width: 33.4%;"><?php _e ('Side Column', 'fuse'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th style="width: 66.6%;"><?php _e ('Main Column', 'fuse'); ?></th>
                            <th style="width: 33.4%;"><?php _e ('Side Column', 'fuse'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr>
                            <td style="width: 66.6%;">
                                <div id="fuse_posttype_builder_metaboxes_main">
                                    <p class="none"><?php _e ('No meta boxes set', 'fuse'); ?></p>
                                </div>
                            </td>
                            <td style="width: 33.4%;">
                                <div id="fuse_posttype_builder_metaboxes_side">
                                    <p class="none"><?php _e ('No meta boxes set', 'fuse'); ?></p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p>
                    <?php _e ('Add a new meta box:', 'fuse'); ?>
                    <input type="text" id="fuse_builder_new_metabox_name" name="fuse_builder_new_metabox_name" value="" placeholder="<?php esc_attr_e ('Meta box name', 'fuse'); ?>" />
                    <select id="fuse_builder_new_metabox_location" name="fuse_builder_new_metabox_location">
                        <option value="main"><?php _e ('Main Column', 'fuse'); ?></option>
                        <option value="side"><?php _e ('Side Column', 'fuse'); ?></option>
                    </select>
                    <button id="fuse_builder_new_metabox_add" class="button"><?php _e ('Add Meta Box', 'fuse'); ?></button>
                </p>
                
                
                <!-- These are our HTML templates  -->
                
                <template id="fuse-builder_meta_box">
                    <?php
                        echo $this->_metaboxTemplateHtml ();
                    ?>
                </template>
            <?php
        } // metaboxesMeta ()
        
        
        
        
        /**
         *  Save our posts values.
         */
        public function savePost ($post_id, $post) {
            global $wpdb;
            
            // Labels
            if (array_key_exists ('fuse_posttype_builder_labels_advanced', $_POST)) {
                $advanced = $_POST ['fuse_posttype_builder_labels_advanced'];
                $labels = $_POST ['fuse_posttype_builder_labels'];
                
                if ($advanced != 'yes') {
                    $advanced = 'no';
                    
                    $model = new Model\PostType\Builder ($post);
                    
                    foreach ($model->getLabels () as $key => $label) {
                        if ($label ['type'] == 'advanced') {
                            unset ($labels [$key]);
                        } // if ()
                    } // foreach ()
                } // if ()
                
                update_post_meta ($post_id, 'fuse_posttype_builder_labels_advanced', $advanced);
                update_post_meta ($post_id, 'fuse_posttype_builder_labels', $labels);
            } // if ()
            
            // Settings
            if (array_key_exists ('fuse_posttype_builder_slug', $_POST)) {
                // Check slug
                $new_slug = sanitize_key ($_POST ['fuse_posttype_builder_slug']);
                $current_slug = get_post_meta ($post_id, 'fuse_posttype_builder_slug', true);
                
                $existing_post_types = get_posts (array (
                    'numberposts' => 1,
                    'post_type' => 'any',
                    'meta_query' => array (
                        array (
                            'key' => 'fuse_posttype_builder_slug',
                            'value' => $new_slug
                        )
                    )
                ));
                
                if (count ($existing_post_types) == 0) {
                    if ($new_slug != $current_slug) {
                        
                        $wpdb->update ($wpdb->posts, array (
                            'post_type' => $new_slug
                        ), array (
                            'post_type' => $current_slug
                        ), array (
                            '%s'
                        ), array (
                            '%s'
                        ));
                        
                        update_post_meta ($post_id, 'fuse_posttype_builder_slug', $new_slug);
                    } // if ()
                } // if ()
                
                // Save settings
                update_post_meta ($post_id, 'fuse_posttype_builder_settings', $_POST ['fuse_posttype_builder_settings']);
            } // if ()
        } // savePost ()
        
        
        
        
        /**
         *  Set up a toggle setting.
         */
        protected function _toggleSetting ($key, $setting) {
            $options = array (
                '' => __ ('Default', 'fuse'),
                'no' => __ ('No', 'fuse'),
                'yes' => __ ('Yes', 'fuse')
            );
            
            $value = $setting ['value'];
            
            if ($value == '' && array_key_exists ('default', $setting)) {
                $value = $setting ['default'];
            } // if ()
            ?>
                <select name="fuse_posttype_builder_settings[<?php esc_attr_e ($key); ?>]">
                    <?php foreach ($options as $op_key => $op_label): ?>
                        <option value="<?php esc_attr_e ($op_key); ?>"<?php selected ($value, $op_key); ?>><?php echo $op_label; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php
        } // _toggleSetting ()
        
        /**
         *  Set up an options setting.
         */
        public function _optionsSetting ($key, $setting) {
            $values = array ();
            
            if (array_key_exists ('value', $setting) && is_array ($setting ['value'])) {
                $values = $setting ['value'];
            } // if ()
            elseif (array_key_exists ('default', $setting)) {
                $values = $setting ['default'];
            } // elseif ()
            ?>
                <ul style="margin-top: 0; margin-bottom: 0;">
                    <?php foreach ($setting ['options'] as $op_key => $op_label): ?>
                        <li>
                            <label>
                                <input type="checkbox" name="fuse_posttype_builder_settings[<?php esc_attr_e ($key); ?>][]" value="<?php esc_attr_e ($op_key); ?>"<?php if (in_array ($op_key, $values)) echo ' checked="checked"'; ?> />
                                <?php echo $op_label; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php
        } // optionsSetting ()
        
        /**
         *  Set up a standard field setting.
         */
        protected function _fieldSetting ($key, $setting) {
            $value = $setting ['value'];
            ?>
                <input type="<?php esc_attr_e ($setting ['type']); ?>" name="fuse_posttype_builder_settings[<?php esc_attr_e ($key); ?>]" value="<?php esc_attr_e ($value); ?>" class="regular-text" />
            <?php
        } // _fieldsetting ()
        
        
        
        
        /**
         *  Get a meta box element template.
         *
         *  @param string $name The name of the meta box.
         *  @param array $fields The fields to add to this meta box.
         *
         *  return string The HTML for the emtabox area.
         */
        protected function _metaboxTemplateHtml ($name = '', $fields = array ()) {
            ob_start ();
            ?>
                <div class="fuse-builder-metabox">
                    
                    <div class="metabox-controls">
                        <div class="move-up">
                            <span class="dashicons dashicons-arrow-up" title="<?php esc_attr_e ('Move UP', 'fuse'); ?>"></span>
                        </div>
                        <div class="move-down">
                            <span class="dashicons dashicons-arrow-down" title="<?php esc_attr_e ('Move DOWN', 'fuse'); ?>"></span>
                        </div>
                    </div>
                    
                    <div class="content">
                        <input name="title" class="fuse_posttype_builder_metabox_name" value="<?php esc_attr_e ($name); ?>" />
                            
                        <div class="fuse_posttype_builder_metabox_field">
                            
                            <div class="field-controls">
                                <div class="move-up">
                                    <span class="dashicons dashicons-arrow-up" title="<?php esc_attr_e ('Move UP', 'fuse'); ?>"></span>
                                </div>
                                <div class="move-down">
                                    <span class="dashicons dashicons-arrow-down" title="<?php esc_attr_e ('Move DOWN', 'fuse'); ?>"></span>
                                </div>
                            </div>
                                
                            <input type="text" name="key" value="" placeholder="Data key" class="fuse_posttype_builder_field_key" />
                            
                            <input type="text" name="label" value="" placeholder="Label" class="fuse_posttype_builder_field_label" />
                            <select name="" class="fuse_posttype_builder_field_type">
                                <option value="text"><?php _e ('Text field', 'fuse'); ?></option>
                                <option value="text"><?php _e ('Number', 'fuse'); ?></option>
                            </select>
                            <a href="#" class="fuse_posttype_builder_metabox_field_delete" title="<?php esc_attr_e ('Delete metabox data field', 'fuse'); ?>">
                                <span class="dashicons dashicons-dismiss"></span>
                            </a>
                        </div>
                        
                        
                        
                        
                        
                            
                        <div class="fuse_posttype_builder_metabox_field">
                            
                            <div class="field-controls">
                                <div class="move-up">
                                    <span class="dashicons dashicons-arrow-up" title="<?php esc_attr_e ('Move UP', 'fuse'); ?>"></span>
                                </div>
                                <div class="move-down">
                                    <span class="dashicons dashicons-arrow-down" title="<?php esc_attr_e ('Move DOWN', 'fuse'); ?>"></span>
                                </div>
                            </div>
                                
                            <input type="text" name="key" value="" placeholder="Data key" class="fuse_posttype_builder_field_key" />
                            
                            <input type="text" name="label" value="" placeholder="Label" class="fuse_posttype_builder_field_label" />
                            <select name="" class="fuse_posttype_builder_field_type">
                                <option value="text"><?php _e ('Text field', 'fuse'); ?></option>
                            </select>
                            <a href="#" class="fuse_posttype_builder_metabox_field_delete" title="<?php esc_attr_e ('Delete metabox data field', 'fuse'); ?>">
                                <span class="dashicons dashicons-dismiss"></span>
                            </a>
                        </div>
                        <div class="fuse_posttype_builder_metabox_field">
                            
                            <div class="field-controls">
                                <div class="move-up">
                                    <span class="dashicons dashicons-arrow-up" title="<?php esc_attr_e ('Move UP', 'fuse'); ?>"></span>
                                </div>
                                <div class="move-down">
                                    <span class="dashicons dashicons-arrow-down" title="<?php esc_attr_e ('Move DOWN', 'fuse'); ?>"></span>
                                </div>
                            </div>
                                
                            <input type="text" name="key" value="" placeholder="Data key" class="fuse_posttype_builder_field_key" />
                            
                            <input type="text" name="label" value="" placeholder="Label" class="fuse_posttype_builder_field_label" />
                            <select name="" class="fuse_posttype_builder_field_type">
                                <option value="text"><?php _e ('Text field', 'fuse'); ?></option>
                            </select>
                            <a href="#" class="fuse_posttype_builder_metabox_field_delete" title="<?php esc_attr_e ('Delete metabox data field', 'fuse'); ?>">
                                <span class="dashicons dashicons-dismiss"></span>
                            </a>
                        </div>
                        
                        
                        
                        
                        
                        
                        
                    </div>
                    
                    <div class="delete">
                        <a href="#" class="fuse_posttype_builder_metabox_delete" title="<?php esc_attr_e ('Delete metabox', 'fuse'); ?>">
                            <span class="dashicons dashicons-dismiss"></span>
                        </a>
                    </div>
                       
                </div>
            <?php
            $html = ob_get_contents ();
            ob_end_clean ();
            
            return $html;
        } // _metaboxTemplateHtml ();
        
    } // class Builder