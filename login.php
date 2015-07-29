<?php

require( dirname(__FILE__) . '/../../../wp-load.php' );

    if ( isset( $_POST['du-submitted'] ) ) {
        $email   = sanitize_email( $_POST["du-email"] );
        $redirect   = $_POST["du-redirect"];

        if (email_exists($email)) {
            if (!isset($_GET["emailtaken"])) {
                $redirect .= (strpos($redirect,'?') !== false) ? "&" : "?" . "emailtaken=true";
            }
            wp_redirect( $redirect );
            exit;
        }

        if (empty($email)) {
            if (!isset($_GET["emailrequired"])) {
                $redirect .= (strpos($redirect,'?') !== false) ? "&" : "?" . "emailrequired=true";
            }
            wp_redirect( $redirect );
            exit;
        }

        // generate user data
        $username = 'dummy'.time();
        $password = wp_generate_password();
        $name = 'Dummy'.time();
        $user_data = array(
            'ID' => '',
            'user_pass' => $password,
            'user_login' => $username,
            'user_nicename' => 'Dummy',
            'user_email' => $email,
            'display_name' => 'Dummy',
            'first_name' => $name,
            'last_name' => 'User',
            'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
        );

        // clean output buffer, so we can set the cookie
        ob_clean();

        // create the user
        $user_id = wp_insert_user( $user_data );

        // print_r ($user_id);

        // email logon details for future reference
        $to = [get_option( 'admin_email' ), $email];
        $headers = "From: $name <$email>" . "\r\n";
        $message = "Dummy user has been created at $redirect\n";
        $message .= "Username: $username\n";
        $message .= "Password: $password\n";
        $message .= "Email: $email\n";
        wp_mail( $to, "Dummy user created", $message, $headers );

        // authenicate the user
        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $password;
        $creds['remember'] = false;
        $user = wp_signon( $creds, false );
        wp_set_auth_cookie($user->ID, false);

        // redirect page
        wp_redirect( $redirect );
 //       exit;

    }
?>