<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.codeclouds.com
 * @since      1.0.0
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/admin
 * @author     Codeclouds Team <team@codeclouds.biz>
 */
class Codeclouds_Nft_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_filter( 'plugin_action_links_' . CODECLOUDS_NFT_PLUGIN_SLUG . '/'. CODECLOUDS_NFT_PLUGIN_SLUG . '.php', array($this, 'add_plugin_action_links') );
        add_action( 'admin_init', array($this, 'add_plugin_activation_redirect') );
        add_action( 'admin_menu', array($this, 'register_top_menu_page') );
        add_filter('set-screen-option', array($this, 'nft_orders_set_option'), 10, 3);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_key'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_title'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_domain'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_amount'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_account_address'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_token_id'), 10, 2);
        add_filter('is_protected_meta', array($this, 'is_protected_meta_coupon_meta'), 10, 2);
        add_action( 'woocommerce_order_status_changed', array($this, 'on_order_status_changed'), 99, 3 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Codeclouds_Nft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Codeclouds_Nft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'codeclouds-nft-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/codeclouds-nft-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Codeclouds_Nft_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Codeclouds_Nft_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'codeclouds-nft-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/codeclouds-nft-admin.js', array( 'jquery', 'codeclouds-nft-select2' ), $this->version, true );
        wp_localize_script($this->plugin_name, 'nftAdminJsData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            '_nonce' => wp_create_nonce('CCNFT-ADMIN-DIGEST'),
        ]);

	}

    public function add_plugin_action_links ( $actions ) : array {
        $action_links = array(
            '<a href="' . CODECLOUDS_NFT_SETTINGS_PAGE . '">Settings</a>',
        );
        $actions = array_merge( $actions, $action_links );

        return $actions;
    }

    public function add_plugin_activation_redirect() {
	    if(is_user_logged_in()) {
            if ( intval( get_option( 'codeclouds_nft_activation_redirect', false ) ) === wp_get_current_user()->ID ) {
                delete_option( 'codeclouds_nft_activation_redirect' );
                exit( wp_safe_redirect( CODECLOUDS_NFT_SETTINGS_PAGE ) );
            }
        }
    }

    public function register_top_menu_page(){
        $hook = add_menu_page(
            __( 'Codeclouds NFT', 'codeclouds-nft' ),
            'Codeclouds NFT',
            'manage_options',
            'codeclouds-nft-order',
            array($this, 'get_top_menu_page'),
            plugins_url( CODECLOUDS_NFT_PLUGIN_SLUG . '/admin/images/cc-logo.png' ),
            58
        );

        add_action( "load-$hook", array($this, 'add_page_screen_options') );
    }

    public function add_page_screen_options() {
        $option = 'per_page';
        $args = array(
            'label' => 'Orders',
            'default' => 10,
            'option' => 'orders_per_page'
        );
        add_screen_option( $option, $args );
    }

    public function nft_orders_set_option($status, $option, $value) {
        return $value;
    }

    public function get_top_menu_page(){
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/codeclouds-nft-admin-orders.php';
    }

    public function is_protected_meta_coupon_key($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_COUPON_KEY ? true : $protected;
    }

    public function is_protected_meta_coupon_title($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY . '_title' ? true : $protected;
    }

    public function is_protected_meta_coupon_domain($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY . '_domain' ? true : $protected;
    }

    public function is_protected_meta_coupon_amount($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY . '_amount' ? true : $protected;
    }

    public function is_protected_meta_coupon_account_address($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY . '_account_address' ? true : $protected;
    }

    public function is_protected_meta_coupon_token_id($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY . '_token_id' ? true : $protected;
    }

    public function is_protected_meta_coupon_meta($protected, $meta_key) {
        return $meta_key === CODECLOUDS_NFT_META_KEY ? true : $protected;
    }

    public function on_order_status_changed($order_id, $old_status, $new_status) {
	    /*['pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed', 'checkout-draft']*/
	    //echo $new_status;exit;
    }
}
