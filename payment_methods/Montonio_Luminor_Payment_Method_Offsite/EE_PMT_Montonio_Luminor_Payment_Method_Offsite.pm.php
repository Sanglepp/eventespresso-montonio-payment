<?php

if (!defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}


/**
 *
 * EE_PMT_Onsite
 *
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EE_PMT_Montonio_Luminor_Payment_Method_Offsite extends EE_PMT_Base{
	public $paymentSdk = null;

	/**
	 *
	 * @param EE_Payment_Method $pm_instance
	 * @return EE_PMT_Montonio_Luminor_Payment_Method_Offsite
	 */
	public function __construct($pm_instance = NULL) {
		require_once($this->file_folder().'EEG_Montonio_Luminor_Payment_Method_Offsite.gateway.php');
		$this->_gateway = new EEG_Montonio_Luminor_Payment_Method_Offsite();
		$this->_pretty_name = esc_html__("Montonio Luminor Payment", 'event_espresso');

		$this->paymentSdk = new MontonioPayment();
		parent::__construct($pm_instance);
		$this->_default_button_url = $this->paymentSdk->bankLogos['luminor'];
	}

	/**
	 * Adds the help tab
	 * @see EE_PMT_Base::help_tabs_config()
	 * @return array
	 */
	public function help_tabs_config(){
		return array(
			$this->get_help_tab_name() => array(
				'title' => esc_html__('Montonio Luminor Payment Method Offsite Settings', 'event_espresso'),
				'filename' => 'montonio_luminor_payment_method_offsite',
				'template_args' => array(
					'variable_x' => 'VARIABLE X',
				)
				),
		);
	}



	/**
	 * Creates the billing form for this payment method type
	 * @param \EE_Transaction $transaction
	 * @return NULL
	 */
	public function generate_new_billing_form( EE_Transaction $transaction = NULL ) {
		return NULL;
	}

	/**
	 * Gets the form for all the settings related to this payment method type
	 * @return EE_Payment_Method_Form
	 */
	public function generate_new_settings_form() {
		$form = new EE_Payment_Method_Form(array(
			'extra_meta_inputs'=>array(
				'override_use_separate_IPN_request' => new EE_Yes_No_Input( array(
					'html_label_text' => esc_html__('Use Separate IPN', 'event_espresso'),
					'html_help_text' => esc_html__( 'Instruct the gateway to send a separate IPN request, or send payment data back with the user upon return.', 'event_espresso' ),
				)))));
		return $form;
	}

}

// End of file EE_PMT_Onsite.php