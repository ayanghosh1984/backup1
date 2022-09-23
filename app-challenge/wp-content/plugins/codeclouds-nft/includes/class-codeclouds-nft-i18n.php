<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.codeclouds.com
 * @since      1.0.0
 *
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 * @author     Codeclouds Team <team@codeclouds.biz>
 */
class Codeclouds_Nft_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'codeclouds-nft',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
