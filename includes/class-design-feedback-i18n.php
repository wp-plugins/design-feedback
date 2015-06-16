<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://designfeedbackplugin.com
 * @since      0.1
 *
 * @package    Design_Feedback
 * @subpackage Design_Feedbacke/admin
 * @author     Design Feedback
 */

class Design_Feedback_i18n {

	/**
	 * The domain specified for this plugin.
	 */
	private $domain;

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the domain equal to that of the specified domain.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}
