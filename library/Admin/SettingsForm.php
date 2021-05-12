<?php
    /**
     *  @package fuse-cms-framework
     *
     *  This is our base settings form class. Extends this to create your own forms.
     *
     *  @filter fuse_settings_form_{form_id}_panels
     */
    
    namespace Fuse\Admin;
    
    use Fuse\Admin\SettingsForm\Panel;
    
    
    class SettingsForm {
        
        /**
         *  @var string The form ID.
         */
        public $id;
        
        /**
         *  @var array The arguments for this class.
         */
        protected $_args;
        
        
        
        
        /**
         *  Object constructor.
         *
         *  @param string $form_id The form ID.
         *  @param Fuse\Admin\SettingsForm\Panel|array $panels These are the panels that are going to be displayed on the form.
         *  @param array $args The arguments for this form.
         *          submit_button   The submit button text. Defaults to 'Submit'.
         */
        public function __construct ($form_id, $panels = array (), $args = array ()) {
            $this->id = $form_id;
            
            if (is_array ($panels)) {
                $this->addPanels ($panels);
            } // if ()
            else {
                $this->addPanel ($panels);
            } // else
            
            $this->_args = array_merge (array (
                'submit_button' => __ ('Submit', 'fuse')
            ), $args);
        } // __construct ()
        
        
        
        
        /**
         *  Add a panel to this form.
         *
         *  @param Fuse\Admin\SettingsForm\Panel $panel The panel to add to the form.
         *
         *  @return Fuse\Admin\SettingsForm Returns this form.
         */
        public function addPanel (Panel $panel) {
            $this->_panels [] = $panel;
            
            return $this;
        } // addPanel ()
        
        /**
         *  Add a set of panels to this form.
         *
         *  @param array $panels The panels to add to this form.
         *
         *  @return Fuse\Admin\SettingsForm Returns this form.
         */
        public function addPanels (array $panels) {
            foreach ($panels as $panel) {
                $this->addPanel ($panel);
            } // foreach ()
            
            return $this;
        } // addPanels ()
        
        
        
        
        /**
         *  Get the forms HTML code.
         *
         *  @return string The forms HTML code.
         */
        public function getFormHtml () {
            $panels = apply_filters ('fuse_settings_form_'.$this->id.'_panels', $this->_panels);
            ob_start ();
            
            ?>
                <div id="<?php esc_attr_e ($this->id); ?>" class="fuse-settings-form">
                    
                    <ul class="fuse-settings-form-list">
                        <?php foreach ($panels as $panel): ?>
                            <li>
                                <a href="#fuse-settings-panel-<?php echo $panel->id; ?>"><?php echo $panel->label; ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="fuse-settings-form-panels">
                        <?php foreach ($panels as $panel): ?>
                        
                            <div id="fuse-settings-panel-<?php echo $panel->id; ?>" class="fuse-settings-panel">
                                <?php
                                    echo $panel;
                                ?>
                            </div>
                        
                        <?php  endforeach; ?>
                        
                        <p class="fuse-settings-form-submit-p"><button id="fuse-settings-form-submit" class="button button-primary"><?php echo $this->_args ['submit_button']; ?></button></p>
                    </div>
                    
                </div>
            <?php
            
            $html = ob_get_contents ();
            ob_end_clean ();
            return $html;
        } // getFormHtml ()
        
        
        
        
        /**
         *  Output the forms HTML
         */
        public function __toString () {
            return $this->getFormHtml ();
        } // __toStrong ()
        
    } // class SettingsForm