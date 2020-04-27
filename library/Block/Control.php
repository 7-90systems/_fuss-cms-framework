<?php
    /**
     *  @package    fusecms
     *
     *  This is the base class for our Control classes.
     */
    
    namespace Fuse\Block;
    
    use Fuse\Block\Field;
    
    /**
     * Class Control_Abstract
     */
    abstract class Control {
    
        /**
         *  @var string Control name.
         */
        public $name = '';
    
        /**
         *  @var string Control label.
         */
        public $label = '';
    
        /**
         * 
         *
         *  @var string Field variable type (passed as an attribute when
         *  registering the block in Javascript).
         */
        public $type = 'string';
    
        /**
         *  @var array Control settings.
         */
        public $settings = array ();
    
        /**
         *  @var array Configurations for common settings, like 'help' and 'placeholder'.
         *     An associative array of setting configurations.
         *
         *     @type string $setting_name   The name of the setting, like 'help'.
         *     @type array  $setting_config The default configuration of the setting.
         */
        public $settings_config = array ();
    
        /**
         * @var array The possible editor locations, either in the main block editor, or the inspector controls.
         */
        public $locations = array ();
        
        
        
    
        /**
         *  Object constructor.
         */
        public function __construct ($name, $label) {
            $this->name = $name;
            $this->label = $label;
            
            $this->_createSettingsConfig ();
            $this->_registerSettings ();
        } // __construct ()
        
        
        
        
        /**
         * Register settings.
         *
         * @return void
         */
        abstract protected function _registerSettings ();
        
        
        
        
        /**
         *  Render additional settings in table rows.
         *
         *  @param Field  $field The Field containing the options to render.
         *  @param string $uid   A unique ID to used to unify the HTML name,
         *  for, and id attributes.
         */
        public function renderSettings ($field, $uid) {
            foreach ($this->settings as $setting) {
                // Don't render the location setting for sub-fields.
                if ('location' === $setting->type && isset ($field->settings ['parent'])) {
                    continue;
                } // if ()
    
                // Don't render the field width setting for sub-fields.
                if ('width' === $setting->type && isset ($field->settings ['parent'])) {
                    continue;
                } // if ()
    
                if (isset ($field->settings [$setting->name])) {
                    $setting->value = $field->settings [$setting->name];
                } // if ()
                else {
                    $setting->value = $setting->default;
                } // else
    
                $classes = array (
                    'block-fields-edit-settings-'.$this->name.'-'.$setting->name,
                    'block-fields-edit-'.$setting->name.'-settings',
                    'block-fields-edit-settings-'.$this->name,
                    'block-fields-edit-'.$setting->name.'-settings',
                    'block-fields-edit-settings',
                );
                $name = 'block-fields-settings['.$uid.']['.$setting->name.']';
                $id = 'block-fields-edit-settings-'.$this->name.'-'.$setting->name.'_'.$uid;
                ?>
                    <tr class="<?php echo esc_attr (implode (' ', $classes)); ?>">
                        <td class="spacer"></td>
                        <th scope="row">
                            <label for="<?php echo esc_attr ($id); ?>">
                                <?php echo esc_html ($setting->label); ?>
                            </label>
                            <p class="description">
                                <?php echo wp_kses_post ($setting->help); ?>
                            </p>
                        </th>
                        <td>
                            <?php
                                $method = 'renderSettings'.ucfirst ($setting->type);
                                
                                if (method_exists ($this, $method)) {
                                    $this->$method ($setting, $name, $id);
                                } // if ()
                                else {
                                   $this->renderSettingsText ($setting, $name, $id);
                                } // if ()
                            ?>
                        </td>
                    </tr>
                <?php
            }
        }
    
        /**
         *  Render text settings
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsText ($setting, $name, $id) {
            ?>
                <input
                    name="<?php esc_attr_e ($name); ?>"
                    type="<?php esc_attr_e ($setting->type); ?>"
                    id="<?php esc_attr_e ($id); ?>"
                    class="regular-text"
                    value="<?php esc_attr_e ($setting->getValue ()); ?>" />
            <?php
        } // renderSettingsText ()
    
        /**
         *  Render textarea settings
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsTextarea ($setting, $name, $id) {
            ?>
                <textarea
                    name="<?php esc_attr_e ($name); ?>"
                    id="<?php esc_attr_e ($id); ?>"
                    rows="6"
                    class="large-text"><?php echo esc_html ($setting->getValue ()); ?></textarea>
            <?php
        } // renderSettingsTextarea ()
    
        /**
         *  Render checkbox settings
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsCheckbox ($setting, $name, $id) {
            ?>
                <input
                    name="<?php esc_attr_e ($name); ?>"
                    type="checkbox"
                    id="<?php esc_attr_e ($id); ?>"
                    class=""
                    value="1"
                    <?php checked ('1', $setting->getValue ()); ?> />
            <?php
        } // renderSettingsCheckbox ()
    
        /**
         *  Render number settings.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsNumber ($setting, $name, $id) {
            $this->renderNumber ($setting, $name, $id);
        } // renderSettingsNUmber ()
    
        /**
         *  Render the number settings, forcing the number in the <input> to be
         *  non-negative. This could be 0, 1, 2, etc, but not -1.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsNumber_non_negative ($setting, $name, $id) {
            $this->renderNumber ($setting, $name, $id, true);
        } // renderSettingNUmber_non_negative ()
    
        /**
         *  Render the number settings, optionally outputting a min="0"
         *  attribute to enforce a non-negative value.
         *
         *  @param Control_Setting $setting      The Control_Setting being rendered.
         *  @param string          $name         The name attribute of the option.
         *  @param string          $id           The id attribute of the option.
         *  @param bool            $non_negative Whether to force the number to be non-negative via a min="0" attribute.
         */
        public function renderNumber ($setting, $name, $id, $non_negative = false) {
            ?>
                <input
                    name="<?php esc_attr_e ($name); ?>"
                    type="number"
                    id="<?php esc_attr_e ($id); ?>"
                    class="regular-text"
                    <?php echo $non_negative ? 'min="0"' : ''; ?>
                    value="<?php esc_attr_e ($setting->getValue ()); ?>" />
            <?php
        } // renderNumber ()
    
        /**
         *  Render an array of settings inside a textarea.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsTextarea_array ($setting, $name, $id) {
            $options = $setting->getValue ();
            
            if (is_array ($options)) {
                // Convert the array to text separated by new lines.
                $value = '';
                
                foreach ($options as $option) {
                    if (!is_array ($option)) {
                        $value.= $option.PHP_EOL;
                        continue;
                    } // if ()
                    
                    if (!isset ($option ['value']) || !isset ($option ['label'])) {
                        continue;
                    } // if ()
                    
                    if ($option ['value'] === $option ['label']) {
                        $value.= $option ['label'].PHP_EOL;
                    } // if ()
                    else {
                        $value.= $option ['value'].' : '.$option ['label'].PHP_EOL;
                    } // else
                } // foreach ()
                
                $setting->value = trim ($value);
            } // if ()
            
            $this->renderSettingsTextarea ($setting, $name, $id);
        } // renderSettingsTextarea_array ()
    
        /**
         *  Renders a <select> of locations.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsLocation ($setting, $name, $id) {
            $this->renderSelect ($setting, $name, $id, $this->locations);
        } // renderSettingsLocation ()
    
        /**
         *  Renders a button group of field widths.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         */
        public function renderSettingsWidth ($setting, $name, $id) {
            $widths = array (
                '25' => '25%',
                '50' => '50%',
                '75' => '75%',
                '100' => '100%'
            );
            
            ?>
                <div class="button-group">
                    <?php
                        foreach ($widths as $value => $label) {
                            ?>
                                <input
                                    class="button"
                                    name="<?php echo esc_attr ($name); ?>"
                                    type="radio"
                                    value="<?php echo esc_attr ($value); ?>"
                                    <?php checked ($value, $setting->getValue ()); ?>
                                    />
                                <label><?php echo esc_html ($label); ?></label>
                            <?php
                        }
                    ?>
                </div>
            <?php
        }
    
        /**
         *  Renders a <select> of the passed values.
         *
         *  @param Control_Setting $setting The Control_Setting being rendered.
         *  @param string          $name    The name attribute of the option.
         *  @param string          $id      The id attribute of the option.
         *  @param array           $values {
         *     An associative array of the post type REST slugs.
         *
         *     @type string $rest_slug The rest slug, like 'tags' for the 'post_tag' taxonomy.
         *     @type string $label     The label to display inside the <option>.
         *  }
         */
        public function renderSelect ($setting, $name, $id, $values) {
            ?>
                <select name="<?php esc_attr_e ($name); ?>" id="<?php esc_attr_e ($id); ?>">
                    <?php foreach ($values as $value => $label): ?>
                        <option value="<?php esc_attr_e ($value); ?>" <?php selected ($value, $setting->getValue ()); ?>>
                            <?php echo esc_html ($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php
        } // renderSelect ()
        
        
        
        
        /**
         *  Validate that the value is contained within a list of options, and
         *  if not, return the first option.
         *
         * @param mixed $value The value to be validated.
         * @param array $settings The field settings.
         *
         * @return mixed The validated value.
         */
        public function validateOptions ($value, $settings) {
            if (array_key_exists ('options', $settings) && $value != '') {
                $options = array ();
        
                // Reindex the options into a more workable format.
                array_walk ($settings ['options'],
                    function ($option) use (&$options) {
                        $options [] = $option ['value'];
                    }
                );
        
                if (is_array ($value)) {
                    // Filter out invalid options where multiple options can be chosen.
                    foreach ($value as $key => $option) {
                        if (!in_array ($option, $options, true)) {
                            unset ($value [$key]);
                        } // if ()
                    } // foreach ()
                } // if ()
                else {
                    // If the value is not in the set of options, return an empty string.
                    if (!in_array ($value, $options, true)) {
                        $value = '';
                    } // if ()
                } // else
            } // if ()
            
            return $value;
        } // validateOptions ()
        
        
        
        
        /**
         *  Sanitise checkbox.
         *
         *  @param string $value The value to sanitise.
         *
         *  @return string
         */
        public function sanitizsCheckbox ($value) {
            if ($value != 1) {
                $value = 0;
            } // if ()
            
            return $value;
        } // sanitiseCheckbox ()
    
        /**
         *  Sanitise a non-zero number.
         *
         *  @param string $value The value to sanitise.
         *
         *  @return int
         */
        public function sanitiseNumber ($value) {
            if ( empty( $value ) || '0' === $value ) {
                $value = null;
            } // if ()
            else {
                $value = intval (filter_var ($value, FILTER_SANITIZE_NUMBER_INT));
            } // else
            
            return $value;
        } // sanitiseNumber ()
    
        /**
         *  Sanitise an array of settings inside a textarea.
         *
         *  @param string $value The value to sanitise.
         *
         *  @return array
         */
        public function sanitiseTextareaAssocArray ($value) {
            $rows    = preg_split ('/\r\n|[\r\n]/', $value);
            $options = array ();
    
            foreach ($rows as $key => $option) {
                if ('' != $option) {
                    $key_value = explode (' : ', $option);
        
                    if (count ($key_value) > 1) {
                        $options [$key]['label'] = $key_value [1];
                        $options [$key]['value'] = $key_value [0];
                    } // if ()
                    else {
                        $options [$key]['label'] = $option;
                        $options [$key]['value'] = $option;
                    } // else
                } // if ()
            } // foreach ()
    
            // Reindex array in case of blank lines.
            $options = array_values ($options);
    
            return $options;
        } // sanitiseTextareaAssocArray ()
    
        /**
         * Sanitise an array of settings inside a textarea.
         *
         * @param string $value The value to sanitise.
         *
         * @return array
         */
        public function sanitiseTextareaArray ($value) {
            $rows = preg_split ('/\r\n|[\r\n]/', $value);
            $options = array ();
    
            foreach ($rows as $key => $option) {
                if ('' != $option) {
                    $key_value = explode( ' : ', $option );
        
                    if ( count( $key_value ) > 1 ) {
                        $options[] = $key_value[0];
                    } else {
                        $options[] = $option;
                    }
                } // if ()
            } // foreach ()
    
            // Reindex array in case of blank lines.
            $options = array_values ($options);
    
            return $options;
        } // sanitiseTextareaArray ()
    
        /**
         *  Sanitise a location value.
         *
         *  @param string $value The value to sanitise.
         *
         *  @return array
         */
        public function sanitiseLocation ($value) {
            if (is_string ($value) !== true || array_key_exists ($value, $this->locations) !== true) {
                $value = NULL;
            } // if ()
            
            return $value;
        } // sanitiseLocation ()
        
        
        
        
        /**
         *  Creates the setting configuration. This sets the values for common
         *  settings, to make adding settings more DRY. Then, controls can
         *  simply use the values here.
         */
        protected function _createSettingsConfig () {
            $this->settings_config = array (
                'location' => array (
                    'name' => 'location',
                    'label' => __ ('Field Location', 'fuse'),
                    'type' => 'location',
                    'default' => 'editor',
                    'sanitise' => array ($this, 'sanitiseLocation')
                ),
                'width' => array (
                    'name' => 'width',
                    'label' => __ ('Field Width', 'fuse'),
                    'type' => 'width',
                    'default' => '100',
                    'sanitise' => 'sanitize_text_field'
                ),
                'help' => array (
                    'name' => 'help',
                    'label' => __ ('Help Text', 'fuse'),
                    'type' => 'text',
                    'default' => '',
                    'sanitise' => 'sanitize_text_field'
                ),
                'default' => array (
                    'name' => 'default',
                    'label' => __ ('Default Value', 'fuse'),
                    'type' => 'text',
                    'default' => '',
                    'sanitise' => 'sanitize_text_field',
                ),
                'placeholder' => array (
                    'name' => 'placeholder',
                    'label' => __ ('Placeholder Text', 'fuse'),
                    'type'     => 'text',
                    'default'  => '',
                    'sanitise' => 'sanitize_text_field'
                )
            );
    
            $this->locations = array (
                'editor' => __ ('Editor', 'fuse'),
                'inspector' => __ ('Inspector', 'fuse')
            );
        } // _createSettingsConfig ()
        
    } // abstract class Control