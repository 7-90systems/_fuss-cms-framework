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
            add_filter ('wp_mail_from', array ($this, 'emailFrom'));
            add_filter ('wp_mail_from_name', array ($this, 'emailFromName'));
        } // __construct ()




        /**
         *  Set the 'from' email address.
         */
        public function emailFrom ($email) {
            $tmp_from = get_option('fuse_email_sender_email_id');

            if (empty ($tmp_from) === false) {
                $email = $tmp_from;
            } // if ()

            return $email;
        } // emailFrom ()

        /**
         *  Set the 'from' name.
         */
        public function emailFromName ($name) {
            $tmp_name = get_option('fuse_email_sender_id');

            if (empty ($tmp_name) === false) {
                $name = $tmp_name;
            } // if ()

            return $name;
        } // emailFromName ()

    } // class EmailSender