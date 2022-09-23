<?php
/**
 * Woocommerce Integration.
 *
 * @package   Woocommerce CC NFT Integration
 * @category Integration
 * @author   CodeClouds.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'WC_CC_NFT_Integration' ) ) :
class WC_CC_NFT_Integration extends WC_Integration {
  /**
   * Init and hook in the integration.
   */
public function __construct() {
    global $woocommerce;
    $this->id                       = 'codeclouds-nft-settings';
    $this->method_title             = __( 'CodeClouds NFT Details');
$this->method_description           = __( 'Put your CodeClouds NFT Details here to activate');
    // Load the settings.
    $this->init_form_fields();
    $this->init_settings();
    // Define user set variables.
    $this->cc_nft_contract_address  = $this->get_option( 'cc_nft_contract_address' );
    $this->cc_nft_contract_abi      = $this->get_option( 'cc_nft_contract_abi' );
    $this->cc_nft_allowed_domains      = $this->get_option( 'cc_nft_allowed_domains' );
    // Actions.
    add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
  }
  /**
   * Initialize integration settings form fields.
   */
  public function init_form_fields() {
    $this->form_fields = array(
        'cc_nft_contract_address' => array(
            'title'             => __( 'Contract Address'),
            'type'              => 'text',
            'description'       => __( 'Put your CodeClouds NFT Contract Address'),
            'desc_tip'          => true,
            'default'           => '',
        ),
        'cc_nft_allowed_domains' => array(
            'title'             => __( 'Allowed Domains'),
            'type'              => 'text',
            'description'       => __( 'Put the domains that are allowed with comma (,) separated [e.g. abc.com,xyz.com]'),
            'desc_tip'          => true,
            'default'           => '',
        ),
        'cc_nft_contract_abi' => array(
            'title'             => __( 'Contract ABI'),
            'type'              => 'textarea',
            'description'       => __( 'Put your CodeClouds NFT Contract ABI JSON'),
            'desc_tip'          => true,
            'default'           => '',
            'css' => 'height:1000px;width:50%;'
        ),
    );
  }
}
endif;