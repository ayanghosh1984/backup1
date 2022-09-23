<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.codeclouds.com
 * @since      1.0.0
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 * @author     Codeclouds Team <team@codeclouds.biz>
 */
class Codeclouds_Nft_Activator extends Codeclouds_Nft {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */

    public static function activate() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            wp_die( 'Could not be activated. ' . self::get_admin_notices() );
        }

        if (
            ( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
            ( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
            return;
        }
        add_option( 'codeclouds_nft_activation_redirect', wp_get_current_user()->ID );
    }

    public static function get_admin_notices() : string {
        $plugin = new Codeclouds_Nft();
        return sprintf(
            '%1$s requires WooCommerce version %2$s or higher installed and active. You can download WooCommerce latest version %3$s OR go back to %4$s.',
            '<strong>' . $plugin->plugin_name . '</strong>',
            CODECLOUDS_NFT_WC_VERSION,
            '<strong><a href="https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip">from here</a></strong>',
            '<strong><a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">plugins page</a></strong>'
        );
    }

}