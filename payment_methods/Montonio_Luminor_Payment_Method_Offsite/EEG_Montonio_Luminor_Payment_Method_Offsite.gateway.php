<?php

if (!defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
require_once EE_MONTONIO_PAYMENT_METHOD_PATH . 'payment_methods/MontonioPayment.php';
/**
 *
 * EEG_Mock_Onsite
 *
 * Just approves payments where billing_info[ 'credit_card' ] == 1.
 * If $billing_info[ 'credit_card' ] == '2' then its pending.
 * All others get refused
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Arnold Sanglepp
 *
 */
class EEG_Montonio_Luminor_Payment_Method_Offsite extends EE_Offsite_Gateway{

	/**
	 * This gateway supports all currencies by default. To limit it to
	 * only certain currencies, specify them here
	 * @var array
	 */
	protected $_currencies_supported = EE_Gateway::all_currencies_supported;
	
	/**
	 * Example of site's login ID
	 * @var string
	 */
	protected $_login_id = null;

	protected $paymentSdk = null;
	
	/**
	 * Whether we have configured the gateway integration object to use a separate IPN or not
	 * @var boolean
	 */
	protected $_override_use_separate_IPN = null;
	
	/**
	 * @return EEG_Montonio_Luminor_Payment_Method_Offsite
	 */
	public function __construct() {
		//if the gateway you are integrating with sends a separate instant-payment-notification request
		//(instead of sending payment information along with the user)
		//set this to TRUE
		$this->set_uses_separate_IPN_request( false ) ;

		$this->paymentSdk = new MontonioPayment();

		parent::__construct();
	}
	
	/**
	 * Override's parent so this gateway integration class can act like one that uses
	 * a separate IPN or not, depending on what is set in the payment methods settings form
	 * @return boolean
	 */
	public function uses_separate_IPN_request() {
		if( $this->_override_use_separate_IPN_request !== null ) {
			$this->set_uses_separate_IPN_request( $this->_override_use_separate_IPN_request );
		} 
		return parent::uses_separate_IPN_request();
	}

	/**
	 *
	 * @param arrat $update_info {
	 *	@type string $gateway_txn_id
	 *	@type string status an EEMI_Payment status
	 * }
	 * @param type $transaction
	 * @return EEI_Payment
	 */
	public function handle_payment_update($update_info, $transaction) {
		$payModel = $this->_pay_model;
		
		$payment = $this->paymentSdk->handlePaymentUpdate($payModel, $update_info, $transaction);
		
		return $payment;
	}

	/**
	 *
	 * @param EEI_Payment $payment
	 * @param type $billing_info
	 * @param type $return_url
	 * @param type $cancel_url
	 */
	public function set_redirection_info($payment, $billing_info = array(), $return_url = NULL, $notify_url = NULL, $cancel_url = NULL) {
		global $auto_made_thing_seed;
		if( empty( $auto_made_thing_seed ) ) {
			$auto_made_thing_seed = rand(1,1000000);
		}
		$payment->set_txn_id_chq_nmbr( $auto_made_thing_seed++ );

		$paymentUrl = $this->paymentSdk->getPaymentUrl($payment, 'luminor', $notify_url, $return_url );
		
		$payment->set_redirect_url($paymentUrl);
		$payment->set_redirect_args( array(
			'amount' => $payment->amount(),
			'gateway_txn_id' => $payment->txn_id_chq_nmbr(),
			'return_url' => $return_url,
			'uses_separate_IPN_request' => $this->uses_separate_IPN_request(),
			'ipn_url' => $notify_url,
		));
		return $payment;
	}
}


// End of file EEG_Mock_Onsite.php