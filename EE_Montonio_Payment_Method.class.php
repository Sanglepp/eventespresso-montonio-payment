<?php

// define the plugin directory path and URL
define( 'EE_MONTONIO_PAYMENT_METHOD_BASENAME', plugin_basename( EE_MONTONIO_PAYMENT_METHOD_PLUGIN_FILE ));
define( 'EE_MONTONIO_PAYMENT_METHOD_PATH', plugin_dir_path( __FILE__ ));
define( 'EE_MONTONIO_PAYMENT_METHOD_URL', plugin_dir_url( __FILE__ ));


/**
 * ------------------------------------------------------------------------
 *
 * Class  EE_Montonio_Payment_Method
 *
 * @package			Event Espresso
 * @subpackage		espresso-new-payment-method
 * @author			    Brent Christensen
 *
 *
 * ------------------------------------------------------------------------
 * 
 */
Class  EE_Montonio_Payment_Method extends EE_Addon {

	/**
	 * class constructor
	 */
	public function __construct() {
	}

	public static function register_addon() {
		// register addon via Plugin API
		EE_Register_Addon::register(
			'Montonio_Payment_Method',
			array(
				'version' 					=> EE_MONTONIO_PAYMENT_METHOD_VERSION,
				'min_core_version' => '4.6.0.dev.000',
				'main_file_path' 				=> EE_MONTONIO_PAYMENT_METHOD_PLUGIN_FILE,
				'admin_callback' => 'additional_admin_hooks',
				// if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
				'pue_options'			=> array(
					'pue_plugin_slug' => 'eea-new-payment-method',
					'plugin_basename' => EE_MONTONIO_PAYMENT_METHOD_BASENAME,
					'checkPeriod' => '24',
					'use_wp_update' => FALSE,
					),
				'payment_method_paths' => array(
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Lhv_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Seb_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Swed_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Coop_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Luminor_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Citadele_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Revolut_Payment_Method_Offsite',
					EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/Montonio_Card_Payment_Method_Offsite',
					),
		));
	}



	/**
	 * 	additional_admin_hooks
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function additional_admin_hooks() {
		// is admin and not in M-Mode ?
		if ( is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			add_filter( 'plugin_action_links', array( $this, 'plugin_actions' ), 10, 2 );
		}
	}



	/**
	 * plugin_actions
	 *
	 * Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
	 * @param $links
	 * @param $file
	 * @return array
	 */
	public function plugin_actions( $links, $file ) {
		if ( $file == EE_MONTONIO_PAYMENT_METHOD_BASENAME ) {
			// before other links
			array_unshift( $links, '<a href="admin.php?page=espresso_payment_settings">' . esc_html__('Settings', 'event_espresso') . '</a>' );
		}
		return $links;
	}






}
// End of file EE_Montonio_Payment_Method.class.php
// Location: wp-content/plugins/espresso-new-payment-method/EE_Montonio_Payment_Method.class.php
