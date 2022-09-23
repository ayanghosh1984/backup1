<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.codeclouds.com
 * @since             1.0.0
 * @package           Codeclouds_Nft
 *
 * @wordpress-plugin
 * Plugin Name:       Codeclouds NFT
 * Plugin URI:        https://www.codeclouds.com
 * Description:       An extension in WordPress WooCommerce checkout which allows the user to claim X% of the discount on total order value if they hold the CodeClouds NFT in their wallet.
 * Version:           1.0.0
 * Author:            Codeclouds Team
 * Author URI:        https://www.codeclouds.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       codeclouds-nft
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CODECLOUDS_NFT_VERSION', '1.0.0' );
define( 'CODECLOUDS_NFT_WC_VERSION', '6.7.0' );
define( 'CODECLOUDS_NFT_WEB3_PROVIDER', 'HTTP://127.0.0.1:7545' );
define( 'CODECLOUDS_NFT_ADMIN_PRIVATE_KEY', 'ab1beed3bbf347371441060b4ed350f66c2f64905ec39b48fcbeb3c6f6a72745' );
define( 'CODECLOUDS_NFT_PLUGIN_SLUG', 'codeclouds-nft' );
define( 'CODECLOUDS_NFT_COUPON_KEY', 'codeclouds_nft_coupon' );
define( 'CODECLOUDS_NFT_META_KEY', 'codeclouds_nft_meta_data' );
define( 'CODECLOUDS_NFT_SETTINGS_PAGE', admin_url( 'admin.php?page=wc-settings&tab=integration&section=codeclouds-nft-settings' ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-codeclouds-nft-activator.php
 */
function activate_codeclouds_nft() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codeclouds-nft-activator.php';
	Codeclouds_Nft_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-codeclouds-nft-deactivator.php
 */
function deactivate_codeclouds_nft() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-codeclouds-nft-deactivator.php';
	Codeclouds_Nft_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_codeclouds_nft' );
register_deactivation_hook( __FILE__, 'deactivate_codeclouds_nft' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-codeclouds-nft.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_codeclouds_nft() {

	$plugin = new Codeclouds_Nft();
	$plugin->run();

}

add_action( 'plugins_loaded', 'run_codeclouds_nft' );
