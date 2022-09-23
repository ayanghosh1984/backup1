<?php

/**
 * Plugin Name: CodeCloud NFT
 * Plugin URI: https://www.codeclouds.com/
 * Description: An extension in WordPress WooCommerce checkout which allows the user to claim X% of the discount on total order value if they hold the CodeClouds NFT in their wallet.
 * Author: DOYEL
 * Author URI: https://www.codeclouds.com/
 * Version: 1.0
 */

class Change_Total_On_Checkout {

    public $plugin_version = '1.0';
    public function __construct() {
    	add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'wp_footer', array( &$this, 'cqoc_add_js' ), 10 );
        // add_action( 'wp_head', array( &$this, 'cqoc_add_head_js' ), 10 );
        add_action( 'init', array( &$this, 'cqoc_load_ajax' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'add_multiple_scripts' ), 10 );
    }
	public function init() {
	    // Checks if WooCommerce is installed.
	    if ( class_exists( 'WC_Integration' ) ) {
	      // Include our integration class.
	      include_once 'includes/class-wc-integration.php';

	      // Register the integration.
	      add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
          add_filter( 'login_redirect',           array( $this, 'my_login_redirect' ) );
          add_filter( 'logout_redirect',           array( $this, 'my_logout_redirect' ) );
	    }
	  }
    /**
     * Add a new integration to WooCommerce.
     */
    public function add_integration( $integrations ) {
        $integrations[] = 'WC_CC_NFT_Integration';
        return $integrations;
    }
    function add_multiple_scripts() {
        $plugin_version = $this->plugin_version;

        wp_enqueue_style( 'custom_style', plugins_url( '/css/ccStyle.css', __FILE__ ), '', $plugin_version, false );
    }
    function cqoc_add_js(){
        if (  is_checkout() ) {
        
        $plugin_version = $this->plugin_version;
        wp_enqueue_script( 'web3_js', 'https://cdnjs.cloudflare.com/ajax/libs/web3/1.7.5/web3.min.js', '', $plugin_version, false );
        wp_enqueue_script( 'custom_js', plugins_url( '/js/custom.js', __FILE__ ), '', $plugin_version, false );
        wp_enqueue_script( 'custom_web3_login_js', plugins_url( '/js/custom-web3-login.js', __FILE__ ), ['web3_js'], $plugin_version, false );
        $cc_nft_json = file_get_contents('wp-content/plugins/codeclouds-nft/contracts/CodeCloudsNFT.json');
        $cc_nft_json_data = json_decode($cc_nft_json,true);
        wp_localize_script('custom_web3_login_js', 'nftJsData', [
            'contractData' => $cc_nft_json_data,
            'contractAddress' => '0x70F3E5d40E94020e03B9255EFb60e50fB93ECdf9'
        ]);
        wp_localize_script('custom_web3_login_js', 'cartDetails', [
            'subTotal' => WC()->cart->get_subtotal()
        ]);
        wp_localize_script('custom_web3_login_js', 'ajaxObject', [
            'ajaxurl' => plugin_dir_url(__FILE__).'codeclouds-nft-ajax.php'
        ]);
        ?>  
            <script type="text/javascript">
                jQuery( function($){
                    if (typeof wc_checkout_params === 'undefined')
                        return false;

                    /* apply cc-nft */
                    $('form.checkout').on('click', '#apply_cc_coupon', function(){
                        $('#cc_nft_coupon_error').hide();
                        $('#cc_nft_coupon_success').hide();
                        var amount = $("input[name='ccNFTRadio']:checked").val();
                        console.log(amount);
                        if(amount > 0) {
                            $.ajax( {
                                type: 'POST',
                                url: wc_checkout_params.ajax_url,
                                data: {
                                    'action': 'enable_discount',
                                    'discount_toggle': '1',
                                    'discount_amount': amount,
                                },
                                success: function (result) {
                                    $('#cc-coupon-form').hide();
                                    $('#cc_nft_coupon_success').show();
                                    $('body').trigger('update_checkout');
                                },
                            });
                        }
                        else {
                            $('#cc_nft_coupon_error').show();
                        }
                    });
                    
                    /* remove cc-nft */
                    $('form.checkout').on('click', '#removeccNft', function(){
                        console.log('remove');
                        $.ajax( {
                            type: 'POST',
                            url: wc_checkout_params.ajax_url,
                            data: {
                                'action': 'enable_discount',
                                'discount_toggle': '1',
                                'remove_discount_amount': '1',
                            },
                            success: function (result) {
                                $('#cc_nft_coupon_success').hide();
                                var x = document.getElementById("ccCoupon");
                                if (window.getComputedStyle(x).display != "none") { 
                                    $("#ccCoupon").toggleClass("toggle-cc-coupon");
                                }

                                $('body').trigger('update_checkout');
                            },
                        });
                    });
                });
            </script>
         <?php  
         }
    }

    function cqoc_load_ajax() {

        if ( !is_user_logged_in() ){
            add_action( 'woocommerce_checkout_before_customer_details',            array( &$this, 'codeclouds_nft_wallet_login' ) );
            add_action( 'wp_ajax_nopriv_cqoc_update_order_review',  array( &$this, 'cqoc_update_order_review' ) );
            add_action( 'wp_ajax_nopriv_enable_discount',           array( &$this, 'checkout_enable_discount_ajax' ) );

        } else{
            add_action( 'woocommerce_checkout_before_customer_details',         array( &$this, 'codeclouds_custom_coupon_nft_field' ) );
            add_action( 'woocommerce_cart_calculate_fees',          array( &$this, 'checkout_set_discount' ) );
            add_action( 'wp_ajax_enable_discount',                  array( &$this, 'checkout_enable_discount_ajax' ) );
        }
    }

    function my_login_redirect() {
        $user = wp_get_current_user();
        $currentUrl = home_url($_SERVER['REQUEST_URI']);
        // if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        //     $url = $user->has_cap( 'administrator') ? admin_url() : wc_get_checkout_url();
        // }
        $url = (str_contains($currentUrl, 'wp-login')) ? admin_url() : wc_get_checkout_url();

        return $url;
    }

    function my_logout_redirect() {
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $user_has_admin_role = in_array( 'administrator', $user_roles );
 
        if ( $user_has_admin_role ) 
            $url = admin_url();
        else
            $url = home_url();

        return $url;
    }

    function codeclouds_nft_wallet_login() {
        echo    '<div>
                    <button type="button" id="loginButton" onclick="">
                    Login with MetaMask
                    </button>
                    <p id="userWallet"></p>
                    <p id="userWalletBalance"></p>
                </div>
        ';
    }

    function codeclouds_custom_coupon_nft_field() {
        $discount_amount = WC()->session->get('discount_amount');
        if($discount_amount) {
            echo 
                '<div id="cc-nft-section">
                    <div class="cc-nft-coupon"> 
                        Have a CodeClouds NFT? <a href="javascript:void(0);" class="showCCcoupon">Claim your discount</a>
                    </div>
                    <div id="ccCoupon" class="toggle-cc-coupon cc-coupon-section">
                        <div id="emptyCouponListSection"></div>
                        <div id="cc-coupon-form" style="display: none;">
                            <div id="couponListSection"></div>
                            <p id="cc_nft_coupon_error" class="error" style="display:none">Please select one NFT to continue</p>
                            <button type="button" class="button" id="apply_cc_coupon">Apply</button>
                        </div>
                        <div class="row" id="cc_nft_coupon_success">
                            <div class="success col-10">Codeclouds NFT coupon applied successfully!!</div>
                            <div class="error col-2" style="cursor: pointer;" align="right" id="removeccNft">Remove</div>
                        </div>
                    </div>
                </div>';
        }
        else {
            echo 
                '<div id="cc-nft-section">
                    <div class="cc-nft-coupon"> 
                        Have a CodeClouds NFT? <a href="javascript:void(0);" class="showCCcoupon">Claim your discount</a>
                    </div>
                    <div id="ccCoupon" class="toggle-cc-coupon cc-coupon-section">
                        <div id="emptyCouponListSection"></div>
                        <div id="cc-coupon-form">
                            <div id="couponListSection"></div>
                            <p id="cc_nft_coupon_error" class="error" style="display:none">Please select one NFT to continue</p>
                            <button type="button" class="button" id="apply_cc_coupon">Apply</button>
                        </div>
                        <div class="row" id="cc_nft_coupon_success" style="display: none;">
                            <div class="success col-10">Codeclouds NFT coupon applied successfully!!</div>
                            <div class="error col-2" style="cursor: pointer;" align="right" id="removeccNft">Remove</div>
                        </div>
                    </div>
                </div>';
        }
    }

    function checkout_enable_discount_ajax() {
        if ( isset($_POST['discount_toggle']) ) {
            WC()->session->set('enable_discount', esc_attr($_POST['discount_toggle']) ? true : false );

            WC()->session->set('discount_amount', $_POST['discount_amount']);

            echo esc_attr($_POST['discount_toggle']);
        }
        wp_die();
    }

    function checkout_set_discount( $cart ) {

        if ( ( is_admin() && ! defined('DOING_AJAX') ) || ! is_checkout() )
            return;

        if( WC()->session->get('discount_amount') ) {
            $ccNftDiscount = WC()->session->get('discount_amount');
            // $ccNftDiscount = 100;
            
        
            if( WC()->session->get('enable_discount') ) {
                $cart->add_fee( __( "Include Codeclouds NFT", "woocommerce" ), -$ccNftDiscount );
            }
        }
        if( WC()->session->get('remove_discount_amount') ) {
            if( WC()->session->get('enable_discount') ) {
                $cart->remove_coupon(  __( "Include Codeclouds NFT", "woocommerce" ));
                WC()->session->__unset('discount_amount');
            }
        }
    }
    

}
$change_total_on_checkout = new Change_Total_On_Checkout( __FILE__ );