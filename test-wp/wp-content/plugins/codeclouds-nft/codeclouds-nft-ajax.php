<?php
$path = preg_replace('/wp-content.*$/', '', __DIR__);
require_once($path."wp-load.php");

/**
 * All ajax request and methods
 *
 * Version: 1.0
 */
class Login_With_Wallet_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_create_user', array( $this, 'create_user_callback' ) );
        add_action( 'wp_ajax_nopriv_create_user', array( $this, 'create_user_callback' ) );
    }

    public function create_user_callback() {
        $response = ['success' => false, 'message' => 'Error occurred while adding user.'];
        if( isset($_POST['wallet_login_success']) && $_POST['wallet_login_success'] && $_POST['account_address'] && !empty($_POST['account_address']) ) {
            $accountAddress = $_POST['account_address'];
            $password = wp_generate_password( 12, true );
            $userData = [
                'user_login' => $accountAddress,
                'user_pass' => $password,
                'display_name' => $accountAddress,
                'first_name' => $accountAddress,
                'last_name' => $accountAddress,
                'role' => get_option('default_role')
            ];
            if(wp_insert_user($userData)) {
                $response = ['success' => true, 'message' => 'User created successfully.'];
            }
        }
        wp_send_json($response);
    }
}

$login_with_wallet_ajax = new Login_With_Wallet_Ajax( __FILE__ );
// create a user after successful metamask login
if(isset($_POST['action']) && $_POST['action'] == 'create_user') {
    $login_with_wallet_ajax->create_user_callback();
}