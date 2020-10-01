<?php
    /**
     *  @package fusecms
     *
     *  This class performs our base administration tasks and sets up the admin
     *  pages for the system.
     *
     *  @action fuse_admin_page_*SECTION*
     *
     *  @filter fuse_admin_page_tabs
     */
    
    namespace Fuse;
    
    
    class Admin {
        
        /**
         *  Object constructor.
         */
        public function __construct () {
            add_action ('admin_menu', array ($this, 'adminMenu'));
            add_action ('admin_init', array ($this, 'registerSettings'));
            
            // Add our admin page sections
            add_action ('fuse_admin_page_emailsender', array ($this, 'emailSender'));
        } // __construct ()
        
        
        
        
        /**
         *  Set up our admin menu items.
         */
        public function adminMenu () {
            add_options_page (__ ('Fuse CMS', 'fuse'), __ ('Fuse CMS', 'fuse'), 'manage_options', 'fuse', array ($this, 'adminPage'));
        } // adminMenu ()
        
        
        
        
        /**
         *  Set up the Fuse admin page.
         */
        public function adminPage () {
            if (array_key_exists ('section', $_GET)) {
                $tab = $_GET ['section'];
            } // if ()
            else {
                $tab = 'emailsender';
            } // else
            ?>
                <div class="wrap">
                    
                    <h1><?php _e ('Fuse CMS for WordPress', 'fuse'); ?></h1>
                    
                    <nav class="nav-tab-wrapper">
                        
                        <a href="?page=fuse&section=emailsender" class="nav-tab<?php if ($tab == 'emailsender') echo ' nav-tab-active' ?>"><?php _e ('Email Sender', 'fuse'); ?></a>
                        
                        <a href="?page=fuse&section=geo" class="nav-tab<?php if ($tab == 'geo') echo ' nav-tab-active' ?>"><?php _e ('Google Geo-Location', 'fuse'); ?></a>
                        
                        <?php
                            do_action ('fuse_admin_page_tabs');
                        ?>
                        
                    </nav>
                    
                    <?php
                        do_action ('fuse_admin_page_'.$tab);
                    ?>
                    
                </div>
            <?php
        } // adminPage ()
        
        
        
        
        /**
         *  Set up teh email sender form.
         */
        public function emailSender () {
            ?>
                <form action="<?php echo esc_url (admin_url ('options.php')); ?>" method="post">
                    <?php
                        do_settings_sections ('fuse_email_sender');
                        settings_fields ('fuse_email_sender_section');
                        submit_button ();
                    ?>
                </form>
            <?php
        } // emailSender ()
        
        /**
         *  Set the overall text for the page.
         */
        public function senderText () {
            ?>
                <p><?php _e ('Set the default email senders details for your site.', 'fuse'); ?></p>
            <?php
        } // senderText ()

        /**
         *  Set up the email sender name field
         */
        public function emailNameField () {
            ?>
                <input name="fuse_email_sender_id" type="text" class="regular-text" value="<?php esc_attr_e (get_option ('fuse_email_sender_id', '')); ?>" placeholder="<?php esc_attr_e ('From Name', 'fuse'); ?>" />
            <?php
        } // emailNameField ()

        /**
         *  Set up the email address field.
         */
        public function emailEmailField () {
            ?>
                <input name="fuse_email_sender_email_id" type="email" class="regular-text" value="<?php esc_attr_e (get_option ('fuse_email_sender_email_id', '')); ?>" placeholder="noreply@yourdomain.com" />
            <?php
        } // emailEmailField ()
        
        
        
        
        /**
         *  Register the settings
         */
        public function registerSettings () {
            add_settings_section ('fuse_email_sender_section', __ ('Set Email Sender', 'fuse'), array ($this, 'senderText'), 'fuse_email_sender');

            add_settings_field ('fuse_email_sender_id', __ ('Email sender name','fuse'), array ($this, 'emailNameField'), 'fuse_email_sender', 'fuse_email_sender_section');
            add_settings_field ('fuse_email_sender_email_id', __ ('Email sender email', 'fuse'), array ($this, 'emailEmailField'), 'fuse_email_sender', 'fuse_email_sender_section');

            register_setting ('fuse_email_sender_section', 'fuse_email_sender_id');
            register_setting ('fuse_email_sender_section', 'fuse_email_sender_email_id');
        } // registerSettings ()
        
    } // class Admin