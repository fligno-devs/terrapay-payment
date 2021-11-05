<?php

namespace FlignoDevs\TerrapayPayment\Models;

use Illuminate\Support\Arr;

/**
 * Class TerrapayPaymentModel
 * @package FlignoDevs
 *
 * @author James Carlo Luchavez
 * @since 2021-11-05
 */
class TerrapayPaymentModel extends BaseModel
{
    /**
     * Unique reference ID generated on the merchant site for the order/invoice that needs to be paid.
     * Length: 5-30
     *
     * Note: So if a duplicate request is received with the exact same OrderId then the gateStatus will be DUPLICATE_TXN_ID.
     * The gatewaySubStatus will be - Duplicate Transaction Id.
     *
     * @required
     * @var string
     */
    public $orderId;

    /**
     * Amount that needs to be collected via this payment request. Should be a positive integer up to 6 decimal places.
     * Length: 1-20
     *
     * @required
     * @var numeric
     */
    public $orderAmount;

    /**
     * 3-letter ISO currency code in upper case.
     * Length: 3
     *
     * @required
     * @var string
     */
    public $orderCurrency;

    /**
     * Free text that gives details of the order. This can be used to display information to the customer at the time of charging.
     * Length: 1-128
     *
     * @var string
     */
    public $message;

    /**
     * Name of the customer where applicable.
     * Length: 1-128
     *
     * @var string
     */
    public $customerName;

    /**
     * Registered wallet number of the customer.
     *
     * @var string
     */
    public $customerMobileNumber;

    /**
     * This is the shortname of the wallet provider. The list of supported wallets in a country can be.
     *
     * @var string
     */
    public $customerWalletName;

    /**
     * This is the customer virtual handle where applicable.
     *
     * @var string
     */
    public $customerVirtualHandle;

    /**
     * Phone number of the customer if the payment instrument is other than a wallet and there is no mobile number available.
     * This will be used in case the customer is to be contacted for payment related issues.
     *
     * @var string
     */
    public $customerPhoneNumber;

    /**
     * Customer email id where applicable.
     * This will be used to send payment receipts or the customer is to be contacted for payment related notifications or issues.
     *
     * @var string
     */
    public $customerEmail;

    /**
     * Unique ID assigned to the merchant during the onboarding process.
     *
     * @var string
     */
    public $merchantId;

    /**
     * Category codes as defined and allocated to the merchant during the onboarding process.
     *
     * @var string
     */
    public $merchantCategoryCode;

    /**
     * Unique ID assigned to each Till/POS machine on the merchant network during the onboarding process.
     *
     * @var string
     */
    public $merchantTillId;

    /**
     * Value can be POS for point of sale devices and ECOM for ecommerce. If the value is empty or not passed, the default value POS will be considered by platform.
     *
     * @var string
     */
    public $transType;

    /**
     * Callback URL that will be used to notify the partner on payment status updates during the process of the transaction.
     *
     * @var string
     */
    public $notifyURL;

    /**
     * Callback URL that will be used to update the final status of the payment once completed.
     *
     * @var string
     */
    public $returnURL;

    /***** ADDITIONAL FIELDS FOR RESPONSE *****/

    /**
     * Unique reference assigned by the payment gateway when a payment request is received. All status checks and updates will include this reference.
     *
     * @var string
     */
    public $gatewayReference;

    /**
     * Timestamp recorded at the time the request was received at the payment gateway.
     *
     * @var string
     */
    public $gatewayTimestamp;

    /**
     * The timezone in which the gatewayTimestamp parameter value is in.
     *
     * @var string
     */
    public $gatewayTimezone;

    /**
     * The status of the request on the gateway.
     * SUCCESS
     * OTP_REQUIRED
     * FAILED
     * TIMEOUT
     * DECLINED
     * (Refer Error code section for further details)
     *
     * @var string
     */
    public $gatewayStatus;

    /**
     * Additional details on the gatewayStatus.
     *
     * @var string
     */
    public $gatewaySubStatus;

    /***** METHODS *****/

    /**
     * Initiate Payment
     *
     * To initiate a payment request. Order details such as a unique order ID, orderAmount and customer details have to be passed over the API.
     *
     * @return $this|array|mixed
     * @throws \JsonException
     */
    public function initiate()
    {
        $data = json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $response = terrapay_payment()->makeRequest(TRUE, 'eig/initiatePayment', $data);

        if ($response->ok()) {
            $this->gatewayReference = $response->json('gatewayReference');
            $this->gatewayTimestamp = $response->json('$this->gatewayTimestamp');
            $this->gatewayTimezone = $response->json('$this->gatewayTimezone');
            $this->gatewayStatus = $response->json('$this->gatewayStatus');
            $this->gatewaySubStatus = $response->json('$this->gatewaySubStatus');
            return $this;
        }

        return $response->json();
    }

    /**
     * Submit OTP
     *
     * To submit the One Time Password (OTP) on transactions where OTP is required to complete the payment.
     * This API is to be called when the initiatePayment API returns a status of OTP_REQUIRED.
     * The partner should display an interface to capture the OTP from the customer and then send it to the gateway using the reference id.
     *
     * @return $this|array|mixed
     */
    public function submitOTP(string $otp)
    {
        $data = [
            'orderId' => $this->orderId,
            'gatewayReference' => $this->gatewayReference,
            'otp' => $otp,
        ];

        $response = terrapay_payment()->makeRequest(TRUE, 'v1/submitOTP', $data);

        if ($response->ok()) {
            return new static($response);
        }

        return $response->json();
    }

    /**
     * Payment Status
     *
     * To check the status of a payment request.
     *
     * @return $this|array|mixed
     */
    public function getStatus()
    {
        $data = [
            'transId' => $this->orderId,
            'merchantId' => $this->merchantId,
        ];

        $response = terrapay_payment()->makeRequest(TRUE, 'eig/paymentStatus', $data);

        if ($response->ok()) {
            return new static($response);
        }

        return $response->json();
    }

    /**
     * Payment Refund
     *
     * To initiate refund of a payment already completed.
     *
     * Payment refund APIs will only work on payments that have already been completed successfully.
     * Successful transaction’s orderId has to be passed as part of refund request and the amount which needs to be refunded.
     * If the payment is not yet completed then an error is returned.
     * Wallet number and other details will be fetched from the orderId passed of successful transaction.
     *
     * @return $this|array|mixed
     * @throws \JsonException
     */
    public function refund()
    {
        $data = json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        $data = Arr::except($data, [
            'transType',
            'gatewayReference',
            'gatewayTimestamp',
            'gatewayTimezone',
            'gatewayStatus',
            'gatewaySubStatus',
        ]);

        $response = terrapay_payment()->makeRequest(TRUE, 'eig/paymentRefund', $data);

        if ($response->ok()) {
            return new static($response);
        }

        return $response->json();
    }

    /**
     * Payment Cancel
     *
     * To initiate cancellation of a payment in progress.
     *
     * Payment cancellation API will only work on payments that are in progress.
     * If the payment is already processed successfully, then this API will return an error and the partner has to use the payment refund API to refund the transactions.
     * Cancellation of a transaction is processed only if the transaction has not yet been submitted to the customer’s Financial Institution (FI).
     *
     * @return $this|array|mixed
     * @throws \JsonException
     */
    public function cancel()
    {
        $data = json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        $data = Arr::except($data, [
            'transType',
            'gatewayReference',
            'gatewayTimestamp',
            'gatewayTimezone',
            'gatewayStatus',
            'gatewaySubStatus',
        ]);

        $response = terrapay_payment()->makeRequest(TRUE, 'v1/payment/cancel', $data);

        if ($response->ok()) {
            return new static($response);
        }

        return $response->json();
    }
}
