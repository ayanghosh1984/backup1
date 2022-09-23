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
    $this->id                 = 'cc-nft-id';
    $this->method_title       = __( 'CC NFT Key');
    $this->method_description = __( 'Put your CC NFT Key here to activate');
    // Load the settings.
    $this->init_form_fields();
    $this->init_settings();
    // Define user set variables.
    $this->custom_name          = $this->get_option( 'cc_nft' );
    // Actions.
    add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
  }
  /**
   * Initialize integration settings form fields.
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'custom_name' => array(
        'title'             => __( 'CC NFT Key'),
        'type'              => 'text',
        'description'       => __( 'Put your CC NFT Key here to activate'),
        'desc_tip'          => true,
        'default'           => '',
        'css'      => 'width:170px;',
      ),
    );
  }
}
endif; 
?>