<?php

class Codeclouds_Nft_Ajax{
    function __construct() {
        $this->defineRoute('CCNftCreateUser', 0);
        $this->defineRoute('CCNftCouponActions', 1);
        $this->defineRoute('CCNftGetUsers', 1);
    }
    private function defineRoute($slug, $isLogged = 0, $condition = true){
        if($condition):
            if($isLogged === 0 || $isLogged === 2):
                add_action( 'wp_ajax_nopriv_'.$slug, [ $this, 'CCNftAjax__'.$slug ] );
            endif;
            if($isLogged === 1 || $isLogged === 2):
                add_action( 'wp_ajax_'.$slug, [ $this, 'CCNftAjax__'.$slug ] );
            endif;
        endif;
    }
    function CCNftAjax__CCNftCreateUser() {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST) &&
            wp_verify_nonce($_POST['nonce'], 'CCNFT-DIGEST') &&
            array_key_exists('account_address', $_POST)
        ){
            $accountAddress = sanitize_text_field($_POST['account_address']);
            if(empty($accountAddress)) wp_send_json_error(['message' => 'No Account Address Found!']);
            $userId = username_exists($accountAddress);
            if(!$userId) { // new user
                try {
                    $password = wp_generate_password( 12, true );
                    $userData = [
                        'user_login' => $accountAddress,
                        'user_pass' => $password,
                        'display_name' => $accountAddress,
                        'first_name' => $accountAddress,
                        'role' => get_option('default_role')
                    ];
                    $userId = wp_insert_user($userData);
                    update_user_meta($userId, 'codeclouds_nft_creation', 1);
                    wp_set_current_user($userId);
                    wp_set_auth_cookie($userId);
                    wp_send_json_success(['message' => 'User created successfully.']);
                }catch(Exception $err) {
                    wp_send_json_error(['message' => $err->getMessage()]);
                }
            } else {
                wp_set_current_user($userId);
                wp_set_auth_cookie($userId);
                wp_send_json_success(['message' => 'User already exists.']);
            }
        }else {
            wp_send_json_error(['message' => 'Invalid Request!']);
        }
    }

    function CCNftAjax__CCNftCouponActions() {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST) &&
            wp_verify_nonce($_POST['nonce'], 'CCNFT-DIGEST') &&
            array_key_exists('enable_discount', $_POST)
        ){
            $enable_discount = absint($_POST['enable_discount']);
            if($enable_discount) {
                $token_meta_data = $_POST['token_meta_data'];

                if(Codeclouds_Nft_Utils::is_coupon_valid($token_meta_data, WC()->cart->total) && WC()->session->get(CODECLOUDS_NFT_COUPON_KEY) !== 1) {
                    WC()->session->set(CODECLOUDS_NFT_META_KEY, $token_meta_data);
                }else {
                    wp_send_json_error(['message' => 'Invalid Coupon. Try another one!']);
                }
            }else {
                $token_meta_data = WC()->session->get(CODECLOUDS_NFT_META_KEY);
                WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
            }
            WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, $enable_discount );

            wp_send_json_success(['message' => $enable_discount ? "Coupon {$token_meta_data['title']} applied successfully" : "Coupon {$token_meta_data['title']} removed successfully"]);
        }else {
            wp_send_json_error(['message' => 'Invalid Request!']);
        }
    }

    function CCNftAjax__CCNftGetUsers() {
        $user_arr = [];
        $total_user_count = 0;
        if(wp_verify_nonce($_GET['nonce'], 'CCNFT-ADMIN-DIGEST')){
            $page = ( ! empty($_GET['page'] ) ) ? absint($_GET['page']) : 1;
            $search = ( ! empty($_GET['search'] ) ) ? absint($_GET['search']) : 1;
            $users_per_page = 3;
            $offset = $users_per_page * ($page - 1);

            $args  = array(
                's'         => $search,
                'number'    => $users_per_page,
                'offset'    => $offset,
                'orderby'   => 'ID'
            );

            $wp_user_query = new WP_User_Query($args);
            $total_user_count = $wp_user_query->get_total();
            $wp_users = $wp_user_query->get_results();
            foreach ($wp_users as $key => $wp_user) {
                $user_info = get_userdata($wp_user->ID);
                $user_arr[$key] = array(
                    'id' => $wp_user->ID,
                    'text' => $user_info->first_name .' '.$user_info->last_name . '(#'.$wp_user->ID.' - '. $user_info->user_email .')'
                );
            }
        }
        $result = array(
            'items' => $user_arr,
            'total_count' => $total_user_count
        );
        wp_send_json($result);
        die();
    }
}
