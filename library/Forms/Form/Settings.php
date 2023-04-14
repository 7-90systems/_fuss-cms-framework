<?php
    /**
     *  @package fuse-cms-framework
     *
     *  @version 1.0
     *
     *  This is our main settings form.
     *
     *  @filter fuse_settings_form_panels
     *  @filter fuse_settings_form_email_sender_fields
     *  @filter fuse_settings_form_google_api_fields
     *  @filter fuse_settings_form_submit_text
     */
    
    namespace Fuse\Forms\Form;
    
    use Fuse\Forms\Container\Form;
    use Fuse\Forms\Component;
    
    
    
    class Settings extends Form {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            $panels = apply_filters ('fuse_settings_form_panels', array (
                new Component\Panel ('email_sender', __ ('Email Sender', 'fuse'), apply_filters ('fuse_settings_form_email_sender_fields', array (
                    new Component\Field\Text ('fuse_email_from_name', __ ('Send from name', 'fuse'), get_fuse_option ('fuse_email_from_name', get_bloginfo ('name')), array (
                        'placeholder' => 'Enter the senders name here',
                        'required' => true
                    )),
                    new Component\Field\Email ('fuse_email_from_email', __ ('Send from email', 'fuse'), get_fuse_option ('fuse_email_from_email', ''), array (
                        'placeholder' => 'Enter the senders email address here',
                        'required' => true,
                        'class' => 'full'
                    ))
                ))),
                new Component\Panel ('google_api', __ ('Google API', 'fuse'), apply_filters ('fuse_settings_form_google_api_fields', array (
                    new Component\Field\Text ('google_api_key', __ ('Google API Key'), get_fuse_option ('google_api_key', ''), array (
                        'class' => 'full',
                        'description' => __ ('Please make sure that this Google API key is available for every Google API that is needed for your site.', 'fuse')
                    ))
                ))),
                new Component\Panel ('theme_features', __ ('Theme Features', 'fuse'), apply_filters ('fuse_settings_form_theme_features_fields', array (
                    new Component\Field\Toggle ('html_fragments', __ ('Enable HTML Fragments'), get_fuse_option ('html_fragments', false))
                )))
            ));
            
            $args = array (
                'id' => 'fuse-settings-form',
                'method' => 'post',
                'action' => esc_url (admin_url ('admin.php?page=fusesettings')),
                'action_bar' => new \Fuse\Forms\Component\ActionBar (array (
                    new Component\Button (apply_filters ('fuse_settings_form_submit_text', __ ('Save settings', 'fuse')))
                ))
            );
            
            parent::__construct ($panels, $args);
        } // __construct ()
        
    } // class Settings