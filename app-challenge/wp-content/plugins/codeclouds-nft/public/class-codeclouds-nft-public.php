<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.codeclouds.com
 * @since      1.0.0
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/public
 * @author     Codeclouds Team <team@codeclouds.biz>
 */
class Codeclouds_Nft_Public {

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
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $cc_nft_wc_data;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->cc_nft_wc_data =  new WC_CC_NFT_Integration();
        if(!empty($this->cc_nft_wc_data->cc_nft_contract_abi) && !empty($this->cc_nft_wc_data->cc_nft_contract_address)) {
            if ( is_user_logged_in() ){
                add_action('woocommerce_checkout_before_customer_details', array($this, 'set_nft_coupon_field'));
                add_action('woocommerce_cart_calculate_fees', array($this, 'set_nft_discount'));
                add_action( 'woocommerce_after_cart_item_quantity_update', array($this, 'after_cart_item_quantity_update') );
                add_action( 'woocommerce_before_cart_item_quantity_zero', array($this, 'before_cart_item_quantity_zero') );
                add_action('woocommerce_thankyou', array($this, 'update_order_after_placement'), 10, 1);
                add_action( 'woocommerce_check_cart_items', array($this, 'check_cart_items') );
            }else {
                add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'set_nft_wallet_login' ) );
            }
        }
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
        if(!empty($this->cc_nft_wc_data->cc_nft_contract_abi) && !empty($this->cc_nft_wc_data->cc_nft_contract_address) && is_checkout() && !is_wc_endpoint_url( 'order-received' )) {
            wp_enqueue_style( 'codeclouds-nft-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1.0/css/all.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'codeclouds-nft-toastr', plugin_dir_url( __FILE__ ) . 'css/toastr.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( 'codeclouds-nft-waitMe', plugin_dir_url( __FILE__ ) . 'css/waitMe.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/codeclouds-nft-public.css', array(), $this->version, 'all' );
        }

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

        if(!empty($this->cc_nft_wc_data->cc_nft_contract_abi) && !empty($this->cc_nft_wc_data->cc_nft_contract_address) && is_checkout()) {
            if(is_wc_endpoint_url( 'order-received' )) {
                wp_enqueue_script( 'codeclouds-nft-web3', plugin_dir_url( __FILE__ ) . 'js/web3.min.js', '', $this->version, false );
            }else {
                wp_enqueue_script( 'codeclouds-nft-web3', plugin_dir_url( __FILE__ ) . 'js/web3.min.js', '', $this->version, true );
                wp_enqueue_script( 'codeclouds-nft-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js', '', $this->version, true );
                wp_enqueue_script( 'codeclouds-nft-toastr', plugin_dir_url( __FILE__ ) . 'js/toastr.min.js', '', $this->version, true );
                wp_enqueue_script( 'codeclouds-nft-waitMe', plugin_dir_url( __FILE__ ) . 'js/waitMe.min.js', '', $this->version, true );
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/codeclouds-nft-public.js', array( 'jquery', 'codeclouds-nft-web3', 'codeclouds-nft-toastr', 'codeclouds-nft-waitMe' ), $this->version, true );
                wp_localize_script($this->plugin_name, 'nftJsData', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    '_nonce' => wp_create_nonce('CCNFT-DIGEST'),
                    'current_user_id' => get_current_user_id(),
                    'contractData' => $this->cc_nft_wc_data->cc_nft_contract_abi,
                    'allowedDomains' => !empty($this->cc_nft_wc_data->cc_nft_allowed_domains) ? explode(',', $this->cc_nft_wc_data->cc_nft_allowed_domains) : [],
                    'contractAddress' => $this->cc_nft_wc_data->cc_nft_contract_address,
                    'subTotal' => WC()->cart->get_subtotal(),
                    'enableDiscount' => WC()->session->get(CODECLOUDS_NFT_COUPON_KEY),
                    'tokenMetaData' => is_array(WC()->session->get(CODECLOUDS_NFT_META_KEY)) ? WC()->session->get(CODECLOUDS_NFT_META_KEY) : []
                ]);
            }
        }

	}

    public function set_nft_discount( $cart ) {
        if ( ( is_admin() && ! defined('DOING_AJAX') ) || ! is_checkout() )
            return;

        if( WC()->session->get(CODECLOUDS_NFT_COUPON_KEY) && is_array(WC()->session->get(CODECLOUDS_NFT_META_KEY)) && count(WC()->session->get(CODECLOUDS_NFT_META_KEY)) ) {
            $token_meta_data = WC()->session->get(CODECLOUDS_NFT_META_KEY);
            $ccNftDiscount = isset($token_meta_data['amount']) ? $token_meta_data['amount'] : 0;
            if(Codeclouds_Nft_Utils::is_coupon_valid($token_meta_data, $cart->cart_contents_total)) {
                $ccNftTitle = !empty($token_meta_data['title']) ? 'Include Codeclouds NFT (' . $token_meta_data['title'] . ')' : 'Include Codeclouds NFT';
                $cart->add_fee( __( $ccNftTitle, "woocommerce" ), -$ccNftDiscount );
            }else {
                WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
                WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, 0 );
            }
        }
    }

    public function after_cart_item_quantity_update($cart_item_key = '', $quantity = '', $old_quantity = '', $cart = array()){
        if( WC()->session->get(CODECLOUDS_NFT_COUPON_KEY) && is_array(WC()->session->get(CODECLOUDS_NFT_META_KEY)) && count(WC()->session->get(CODECLOUDS_NFT_META_KEY)) ) {
            $token_meta_data = WC()->session->get(CODECLOUDS_NFT_META_KEY);
            if(!Codeclouds_Nft_Utils::is_coupon_valid($token_meta_data, $cart->cart_contents_total))  {
                WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
                WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, 0 );
            }
        }
    }

    public function before_cart_item_quantity_zero($cart_item_key) {
        WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
        WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, 0 );
    }

    public function check_cart_items() {
        if( WC()->session->get(CODECLOUDS_NFT_COUPON_KEY) && is_array(WC()->session->get(CODECLOUDS_NFT_META_KEY)) && count(WC()->session->get(CODECLOUDS_NFT_META_KEY)) ) {
            $token_meta_data = WC()->session->get(CODECLOUDS_NFT_META_KEY);
            if(!Codeclouds_Nft_Utils::is_coupon_valid($token_meta_data, WC()->cart->total))  {
                WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
                WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, 0 );
            }
        }
    }

    public function update_order_after_placement($order_id) {
        $order = wc_get_order( $order_id );
        if($order->is_paid()) {
            $cc_nft_coupon_key = WC()->session->get(CODECLOUDS_NFT_COUPON_KEY);
            $cc_nft_meta_data = WC()->session->get(CODECLOUDS_NFT_META_KEY);
            if (isset($cc_nft_coupon_key)) {
                $order->update_meta_data( CODECLOUDS_NFT_COUPON_KEY, $cc_nft_coupon_key );
                if($cc_nft_coupon_key == 1 && is_array($cc_nft_meta_data) && count($cc_nft_meta_data) > 0) {
                    $coupon_title = $cc_nft_meta_data['title'];
                    $coupon_domain = $cc_nft_meta_data['domain'];
                    $coupon_amount = $cc_nft_meta_data['amount'];
                    $account_address = $cc_nft_meta_data['accountAddress'];
                    $token_id = $cc_nft_meta_data['tokenId'];

                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY . '_title', $coupon_title );
                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY . '_domain', $coupon_domain );
                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY . '_amount', $coupon_amount );
                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY . '_account_address', $account_address );
                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY . '_token_id', $token_id );
                    unset($cc_nft_meta_data['title']);
                    unset($cc_nft_meta_data['domain']);
                    unset($cc_nft_meta_data['amount']);
                    unset($cc_nft_meta_data['accountAddress']);
                    unset($cc_nft_meta_data['tokenId']);
                    $order->update_meta_data( CODECLOUDS_NFT_META_KEY, $cc_nft_meta_data );

                    ?>
                    <script>
                        (async function( $ ) {
                            const contractData = `<?php echo $this->cc_nft_wc_data->cc_nft_contract_abi;?>`;
                            const contractAddress = '<?php echo $this->cc_nft_wc_data->cc_nft_contract_address;?>';
                            const accountAddress = '<?php echo $account_address;?>';
                            let tokenId = '<?php echo $token_id;?>';
                            tokenId = parseInt(tokenId);
                            const privateKey = '<?php echo CODECLOUDS_NFT_ADMIN_PRIVATE_KEY;?>';
                            const web3 = new Web3('<?php echo CODECLOUDS_NFT_WEB3_PROVIDER;?>');
                            const contract = new web3.eth.Contract(contractData.abi ? JSON.parse(contractData.abi) : JSON.parse(contractData), contractAddress)
                            const etherAccount = await web3.eth.accounts.privateKeyToAccount(privateKey)
                            await web3.eth.accounts.wallet.add({
                                privateKey: privateKey,
                                address: etherAccount.address
                            });
                            web3.eth.defaultAccount = await etherAccount.address;
                            const tx = await contract.methods.usedCoupon(tokenId, accountAddress);
                            const gas = await tx.estimateGas({from: await etherAccount.address});
                            const gasPrice = await web3.eth.getGasPrice();
                            const data = tx.encodeABI();
                            const receipt = await web3.eth.sendTransaction({
                                from: etherAccount.address,
                                to: contract.options.address,
                                data,
                                gas,
                                gasPrice
                            });

                            console.log('transaction hash', receipt.transactionHash);

                        })( jQuery );
                    </script>
                    <?php
                }
                $order->save();
            }
        }
        $order_user_id = $order->get_user_id();
        $codeclouds_nft_creation = get_user_meta($order_user_id, 'codeclouds_nft_creation', true);

        if ($codeclouds_nft_creation) {
            $order_billing_email = $order->get_billing_email();
            if(!email_exists($order_billing_email)) {
                wp_update_user( array(
                    'ID'         => $order_user_id,
                    'user_email' => $order_billing_email
                ) );
            }
            wp_update_user([
                'ID' => $order_user_id,
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
                'display_name' => $order->get_formatted_billing_full_name(),
                'user_nicename' => strtolower(str_replace(' ', '-', $order->get_formatted_billing_full_name()))
            ]);
            delete_user_meta($order_user_id, 'codeclouds_nft_creation');
        }

        WC()->session->set(CODECLOUDS_NFT_META_KEY, []);
        WC()->session->set(CODECLOUDS_NFT_COUPON_KEY, 0 );
    }

    public function set_nft_coupon_field() {
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/codeclouds-nft-coupon-field.php';
    }

    function set_nft_wallet_login() {
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/codeclouds-nft-wallet-login.php';
    }

}
