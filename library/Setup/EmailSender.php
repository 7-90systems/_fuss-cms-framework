<?php
    /**
     *  @package fusecms
     *
     *  Set up our email sender area.
     */

    namespace Fuse\Setup;


    class EmailSender {

        /**
         *  Object constructor.
         */
        public function __construct () {
            add_action ('admin_init', array ($this, 'emailSenderPage'));
            add_action ('admin_menu', array ($this, 'adminMenu'));

            add_filter ('wp_mail_from', array ($this, 'emailFrom'));
            add_filter ('wp_mail_from_name', array ($this, 'emailFromName'));
        } // __construct ()




        /**
         *  Set up the email sender page.
         */
        public function emailSenderPage () {
            add_settings_section ('fuse_email_sender_section', __ ('Set Email Sender', 'fuse'), array ($this, 'senderText'), 'fuse_email_sender');

            add_settings_field ('fuse_email_sender_id', __ ('Email sender name','fuse'), array ($this, 'emailNameField'), 'fuse_email_sender', 'fuse_email_sender_section');
            add_settings_field ('fuse_email_sender_email_id', __ ('Email sender email', 'fuse'), array ($this, 'emailEmailField'), 'fuse_email_sender', 'fuse_email_sender_section');

            register_setting ('fuse_email_sender_section', 'fuse_email_sender_id');
            register_setting ('fuse_email_sender_section', 'fuse_email_sender_email_id');
        } // emailSenderPage ()

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
         *  Set up our form.
         */
        public function emailSenderOutput () {
?>
    <form action="<?php echo esc_url (admin_url ('options.php')); ?>" method="post">
        <?php
            do_settings_sections ('fuse_email_sender');
            settings_fields ('fuse_email_sender_section');
            submit_button ();
        ?>
    </form>
<?php
        } // emailSenderOutput ()




        /**
         *  Set up the administration menu area.
         */
        public function adminMenu () {
            add_options_page (__ ('Email Sender', 'fuse'), __ ('Email Sender', 'fuse'), 'manage_options', 'email_sender', array ($this, 'emailSenderOutput'));
        } // adminMenu ()




        /**
         *  Set the 'from' email address.
         */
        public function emailFrom ($email) {
            $tmp = get_option('fuse_email_sender_email_id');

            if (empty ($tmp) === false) {
                $email = $tmp;
            } // if ()

            return $email;
        } // emailFrom ()

        /**
         *  Set the 'from' name.
         */
        public function emailFromName ($name) {
            $tmp = get_option('fuse_email_sender_id');

            if (empty ($tmp) === false) {
                $name = $tmp;
            } // if ()

            return $name;
        } // emailFromName ()

    } // class EmailSender