<?php
/*
Plugin Name: Dummy User button
Plugin URI: http://frumbert.github.io/dummy-user
Description: Register demo subscriber at the press of a button using an email address by adding a [dummy-user] shortcode whereever you like.
Version: 1.0
Author: Tim St. Clair
Author URI: http://frumbert.org
License: MIT
*/

// render a form for sign-up
function dummyuser_render() {
        echo '<form method="post" action="' . plugins_url("login.php", __FILE__) . '" class="du-register-form">';
        echo '<input type="hidden" name="du-redirect" value="' . dummyuser_url() .'">';
        echo '<label>Email (required): ';
        echo '<input type="email" name="du-email" value="' . ( isset( $_POST["du-email"] ) ? esc_attr( $_POST["du-email"] ) : '' ) . '" size="40" />';
        echo '</label>';

        echo '<label>Name (optional): ';
        echo '<input type="text" name="du-name" value="' . ( isset( $_POST["du-name"] ) ? esc_attr( $_POST["du-name"] ) : '' ) . '" size="40" />';
        echo '</label>';

        echo '<input type="submit" name="du-submitted" value="Register"/>';
        echo '</form>';
        if (isset($_GET['emailtaken']) && $_GET['emailtaken'] == "true") {
            echo "<p class='du-email-taken'>Sorry, this email address has already been used.</p>";
        }
        if (isset($_GET['emailrequired']) && $_GET['emailrequired'] == "true") {
            echo "<p class='du-email-required'>Sorry, an email address is required.</p>";
        }
}

// url of this page
function dummyuser_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if (isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    $pageURL = str_replace("emailrequired=true", "", $pageURL);
    $pageURL = str_replace("emailtaken=true", "", $pageURL);
    return $pageURL;
}

// do the sign in and redirect
function dummyuser_capture() {
    if ( isset( $_POST['du-submitted'] ) ) {
        $nicename = "Dummy";
        $email   = sanitize_email( $_POST["du-email"] );
        if (isset($_POST["du-name"])) {
            $nicename = sanitize_title( $_POST["du-name"] );
        }
        $redirect   = $_POST["du-redirect"];

        // generate user data
        $username = 'dummy'.time();
        $password = wp_generate_password();
        $name = 'Dummy'.time();
        $user_data = array(
            'ID' => '',
            'user_pass' => $password,
            'user_login' => $username,
            'user_nicename' => $nicename,
            'user_email' => $email,
            'display_name' => 'Dummy',
            'first_name' => $name,
            'last_name' => 'User',
            'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
        );

        // clean output buffer (maybe), so we can set the cookie
        ob_clean();

        // create the user
        $user_id = wp_insert_user( $user_data );

        // email logon details for future reference
        $to = [get_option( 'admin_email' ), $email];
        $headers = "From: $name <$email>" . "\r\n";
        $message = "Dummy user has been created at $redirect\n";
        $message .= "Username: $username\n";
        $message .= "Password: $password\n";
        $message .= "Email: $email\n";
        $message .= "Nice Name: $nicename\n";
        wp_mail( $to, "Dummy user created", $message, $headers );

        // authenicate the user
        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $password;
        $creds['remember'] = true;
        $user = wp_signon( $creds, true );
        wp_set_auth_cookie($user->ID, true);
// print_r($user);
        // redirect page
        // wp_redirect( $redirect );
        echo "<meta http-equiv='refresh' content='20; url=$redirect'>";
        exit;

    }
}

// handle our shortcode
function dummyuser_shortcode() {
    ob_start();
    if (! is_user_logged_in() ) {
        dummyuser_capture();
        dummyuser_render();
    }
    return ob_get_clean();
}

// register our shortcode
add_shortcode( 'dummy-user', 'dummyuser_shortcode' );

?>
