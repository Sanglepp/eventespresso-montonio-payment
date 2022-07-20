<?php

require_once EE_MONTONIO_PAYMENT_METHOD_PATH . 'lib/MontonioPayments/MontonioPaymentsSDK.php';

/**
 * SDK for Montonio Payments.
 * This class contains methods for starting and validating payments.
 */
class MontonioPayment
{

    /**
     * Montonio Access Key
     *
     * @var string
     */
    protected $accessKey = '';

    /**
     * Montonio Secret Key
     *
     * @var string
     */
    protected $secretKey = '';

    /**
     * Montonio Environment (Use sandbox for testing purposes)
     *
     * @var string 'production' or 'sandbox'
     */
    protected $environment = 'sandbox'; // or 'production';


		/**
     * Montonio merchant name. Visible in payment window
     *
     * @var string 
     */
		protected $merchantName = '';

		/**
     * Montonio merchant email
     *
     * @var string 
     */

		protected $merchantEmail = '';


    public $paymentSdk = null;

		public $bankLogos = [
			'swed' => 'https://public.montonio.com/images/aspsps_logos/swedbank.png', // Swed
			'seb' => 'https://public.montonio.com/images/aspsps_logos/seb.png', // Seb
			'lhv' => 'https://public.montonio.com/images/aspsps_logos/lhv.png', // Lhv
			'luminor' => 'https://public.montonio.com/images/aspsps_logos/luminor.png', // Luminor
			'coop' => 'https://public.montonio.com/images/aspsps_logos/coop.png', // Coop
			'citadele' => 'https://public.montonio.com/images/aspsps_logos/citadele.png', // Citadele
			'revolut' => 'https://public.montonio.com/images/aspsps_logos/revolut.png', // Revolut
		];

		public $banks = [
			'swed' => 'HABAEE2X', // Swed
			'seb' => 'EEUHEE2X', // Seb
			'lhv' => 'LHVBEE22', // Lhv
			'luminor' => 'RIKOEE22', // Luminor
			'coop' => 'EKRDEE22', // Coop
			'citadele' => 'PARXEE22', // Citadele
			'revolut' => 'RVUALT2V', // Revolut
			'card' => 'CARD', // Card
		];


    public function __construct()
    {
        $this->paymentSdk = new MontonioPaymentsSDK(
            $this->accessKey,
            $this->secretKey,
            $this->environment
		);
    }


    public function getPaymentUrl($payment, $provider, $notify_url, $return_url)
    {

				$events = $payment->get_first_event();
				$event_list = array();
				$event_list_ids = array();
				if(is_array($events)){
					foreach($events AS $event){
						$event_list_ids[] = $event->ID();
					}
				} else {
					$event_list_ids[] = $events->ID();
				}
				$paymentDescription = 'Registreerimine sündmusele:' .  " " . implode('; ', $event_list_ids);
				if(strlen($paymentDescription) > 93){
					$paymentDescription = implode('; ', $event_list_ids);
				}

				$atendee__first_name = $payment->get_primary_attendee()->fname();
				$atendee__last_name = $payment->get_primary_attendee()->lname();

        $paymentData = array(
				'amount'                    => $payment->amount(), // Make sure this is a float
				'currency'                  => 'EUR', // Currently only EUR is supported
				'merchant_reference'        => $payment->txn_id_chq_nmbr(), // The order id in your system
				'merchant_name'             => $this->merchantName,
				'checkout_email'            => $this->merchantEmail,
				'payment_information_unstructured' => $paymentDescription,
				'merchant_notification_url' => $notify_url, // We will send a webhook after the payment is complete
				'merchant_return_url'       => $return_url, // Where to redirect the customer to after the payment
				'preselected_country'       => 'EE',
				'preselected_aspsp'         => $this->banks[$provider], // The preselected ASPSP identifier
				'preselected_locale'        => 'et', // See available locale options in the docs,
				'checkout_first_name'				=> $atendee__first_name,
				'checkout_last_name'				=> $atendee__last_name
		);

		$this->paymentSdk->setPaymentData($paymentData);
		$paymentUrl = $this->paymentSdk->getPaymentUrl();

        return $paymentUrl;
    }


    public function handlePaymentUpdate($payModel, $update_info, $transaction) {
		
			$token = $update_info[ 'payment_token' ];
			
			if( !$token ){
				return NULL;
			}

			$decoded = $this->paymentSdk->decodePaymentToken($update_info[ 'payment_token' ], $this->secretKey);

			$payment = $payModel->get_payment_by_txn_id_chq_nmbr($decoded->merchant_reference);


			if (
					$payment instanceof EEI_Payment &&
					$decoded->access_key === $this->accessKey &&
					$decoded->status === 'finalized'
			) {
					$payment->set_status( $payModel->approved_status() );
					$payment->set_gateway_response( esc_html__( 'Payment Approved', 'event_espresso' ));
			} else {
					$payment->set_status(  $payModel->failed_status() );
					$payment->set_gateway_response( esc_html__( 'Payment Failed', 'event_espresso' ) );
			}
			return $payment;
		}

		public function fetchBankList()
		{
			return $this->paymentSdk->fetchBankList();
		}

}
