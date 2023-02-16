<?php

namespace ShabnamYusifzada\Pulpal\Api\v1;

use ShabnamYusifzada\Pulpal\Utilities\Curl;
/**
 * PulPal Payment API for transferring funds from wallet to the card
 *
 * @package php-pulpal
 * @author Shabnam Yusifzada <yusifzade.shebnem@gmail.com>
 * @version 1.0.0
 *
 */
class TopUpPayment
{
    use Curl;

    /**
     * Route for product creating in the merchant system
     *
     * @var string
     */
    const PRODUCT_GENERATION_API_PATH = '/api/merchant_payment/External/product';

    /**
     * Route for payment initialization
     *
     * @var string
     */
    const PAYMENT_INITIALIZATION_API_PATH = '/api/merchant_payment/External/register';

    /**
     * Route for checking transaction status
     *
     * @var string
     */
    const CHECK_PAYMENT_API_PATH = '/api/merchant_payment/External/info';

    /**
     * Route for fetching merchant wallet balance
     *
     * @var string
     */
    const WALLET_BALANCE_API_PATH = '/api/merchant_payment/External/balance';

    /**
     * Route for fetching merchant wallet balance with blocked funds
     *
     * @var string
     */
    const WALLET_BALANCE_V2_API_PATH = '/api/merchant_payment/External/balance-v2';

    /**
     * PulPal API host
     *
     * @var string $host
     */
    private $host;

    /**
     *  API public key generated it the merchant account
     *
     * @var string $apiPublicKey
     */
    private $apiPublicKey;

    /**
     * API private key generated in the merchant account
     *
     * @var string $apiPrivateKey
     */
    private $apiPrivateKey;

    /**
     * Merchant ID provided in the merchant account
     *
     * @var int $merchantId
     */
    private $merchantId;

    /**
     * Provider ID given by the PulPal support
     *
     * @var int $providerId
     */
    private $providerId;

    /**
     * Language of the payment page shown to the user
     *
     * @var string $lang
     */
    private $lang;

    /**
     * Product unique code in the merchant system
     *
     * @var string $productUniqueCode
     */
    private $productUniqueCode;

    /**
     * Product ID in the merchant system
     *
     * @var string $productId
     */
    private $productId;

    /**
     * Price of the product which user should pay
     *
     * @var int $productPrice
     */
    private $productPrice;

    /**
     * Create a new TopUpPayment object.
     *
     * @param int $merchantId
     * @param int $providerId
     * @param string $apiPublicKey
     * @param string $apiPrivateKey
     * @param string $lang
     */
    public function __construct($host, $merchantId, $providerId, $apiPublicKey, $apiPrivateKey, $lang = 'az')
    {
        $this->host = $host;
        $this->merchantId = $merchantId;
        $this->providerId = $providerId;
        $this->apiPublicKey = $apiPublicKey;
        $this->apiPrivateKey = $apiPrivateKey;
        $this->lang = $lang;
    }

    /**
     * Get product unique code from the merchant system
     *
     * @return string
     */
    public function getProductUniqueCode()
    {
        return $this->productUniqueCode;
    }


    /**
     * Set product unique code for feature use
     *
     * @param string $value
     * @return string
     */
    public function setProductUniqueCode($value)
    {
        return $this->productUniqueCode = $value;
    }

    /**
     * Get product price
     *
     * @return int
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * Set product price
     *
     * @param $value
     * @return void
     */
    public function setProductPrice($value)
    {
        $this->productPrice = $value;
    }

    /**
     * Get product ID from the merchant system
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set product id for the feature use
     *
     * @param string $value
     * @return void
     */
    public function setProductId($value)
    {
        $this->productId = $value;
    }

    /**
     * Generating signature and preparing necessary data for request
     *
     * @param string $path
     * @param array $params
     * @param string $nonce
     * @return array
     */
    private function prepareRequestData($path, array $params, $nonce)
    {
        //serialization of request parameters
        $serializedPaymentParams = json_encode($params, JSON_UNESCAPED_UNICODE);

       /*
       |
       | Generating the signature on the base of HMAC-SHA256 algorithm
       | with the concatenation of the following parameters and its converting from the binary type to base64:
       | API_KEY + NONCE + PATH + BODY_TEXT
       |
       | Verification of the request signature on the PulPal side allows you
       | to check whether the request really came from the merchant.
       |
       */
        $signature = base64_encode(
            hash_hmac(
                'sha256',
                $this->apiPublicKey . $nonce . $path . $serializedPaymentParams,
                base64_decode($this->apiPrivateKey),
                true
            )
        );

        //Request required headers
        $headers = [
            'Merchant-Id: ' . $this->merchantId,
            'Lang: ' . $this->lang,
            'Api-Key: ' . $this->apiPublicKey,
            'Signature: ' . $signature,
            'Content-Type: application/json',
            'Nonce: ' . $nonce,
            'Accept: text/plain'
        ];

        return [
            'headers' => $headers,
            'params' => $serializedPaymentParams
        ];
    }


    /**
     * Get product from the merchant system
     *
     * @param array $data
     * @param string $nonce
     * @return array
     */
    public function getProduct(array $data, $nonce)
    {
        $requestData = $this->prepareRequestData(self::PRODUCT_GENERATION_API_PATH, $data, $nonce);
        $requestHeaders = $requestData['headers'];
        $requestParams = $requestData['params'];

        return $this->sendRequest(
            $this->host.self::PRODUCT_GENERATION_API_PATH,
            'POST',
            $requestParams,
            $requestHeaders
        );
    }

    /**
     * Get product from the merchant system by unique code
     *
     * @param string $productUniqueCode
     * @param string $nonce
     * @return array
     */
    public function getProductByUniqueCode($productUniqueCode, $nonce = '')
    {
        if (!$nonce) $nonce = time();

        $data = [
            'productUniqueCode' => $productUniqueCode,
        ];

        return $this->getProduct($data, $nonce);
    }

    /**
     * Generating new product in the merchant system
     *
     * @param array $name ['az' => 'Test az', 'ru' => 'Test ru', 'en' => 'Test en']
     * @param array $description ['az' => 'Test description az', 'ru' => 'Test description ru', 'en' => 'Test description en']
     * @param array $prices ['AZN' => 100, 'USD' => 170]
     * @param string $externalId
     * @param string $nonce
     * @return array
     */
    public function generateProduct(array $name, array $description, array $prices, $externalId, $nonce = '')
    {
        if (!$nonce) $nonce = time();
        $data = [
            'manualConfirmation' => false,
            'prices' => $prices,
            'externalId' => $externalId,
            'name' => $name,
            'description' => $description,
            'repeatable' => true
        ];

        return $this->getProduct($data, $nonce);
    }

    /**
     * Initialize payment in the merchant system
     *
     * @param string $externalPaymentId
     * @param string $cardHolder
     * @param string $nonce
     * @param string $currency
     * @return array
     */
    public function initializePayment(
        $externalPaymentId,
        $cardHolder,
        $nonce = '',
        $currency = 'AZN'
    )
    {
        if (!$nonce) $nonce = time();

        $data = [
            'providerId' => $this->providerId,
            'productId' => $this->productId,
            'externalPaymentId' => $externalPaymentId,
            'cardData' => [
                "cardHolder" => $cardHolder
            ],
            'price' => $this->productPrice,
            'currency' => $currency,
            'instantRedirect' => true
        ];

        $requestData = $this->prepareRequestData(self::PAYMENT_INITIALIZATION_API_PATH, $data, $nonce);
        $requestHeaders = $requestData['headers'];
        $requestParams = $requestData['params'];

        return $this->sendRequest(
            $this->host.self::PAYMENT_INITIALIZATION_API_PATH,
            'POST',
            $requestParams,
            $requestHeaders
        );
    }

    /**
     * Check transaction status in the merchant system
     *
     * @param array $data ['externalId' => ''] Transaction external ID
     * @param string $nonce
     * @return array
     */
    public function checkPayment(array $data, $nonce = '')
    {
        if (!$nonce) $nonce = time();
        $query = '?' . http_build_query($data, "", "&");
        $requestData = $this->prepareRequestData(self::CHECK_PAYMENT_API_PATH . $query, $data, $nonce);
        $requestHeaders = $requestData['headers'];
        $requestParams = $requestData['params'];

        return $this->sendRequest(
            $this->host.self::CHECK_PAYMENT_API_PATH . $query,
            'GET',
            $requestParams,
            $requestHeaders
        );
    }

    /**
     * Get payment status meaning by its id
     *
     * @param int $id
     * @return string
     */
    public function getPaymentStatusNameById($id)
    {
        $statuses = [
            0 => 'Created (not final)',
            1 => 'Paid (not final)',
            2 => 'Delivered (final)',
            3 => 'Error (final)',
            4 => 'Declined (final)',
            5 => 'Canceled (final)',
            6 => 'Expired (final)',
            7 => 'Reversed (final)',
            8 => 'PartiallyPaid (not final)',
            9 => 'PartiallyReversed (not final)',
            10 => 'ReversedDelivered',
            11 => 'PartiallyReversedDelivered'
        ];

        return $statuses[$id];
    }

    /**
     * Get transaction status by payment external ID
     *
     * @param array $data ['externalId' => '']
     * @return array
     */
    public function getStatusByPaymentExternalId(array $data)
    {
        $result = $this->checkPayment($data);
        if (isset($result['data']['status'])) {
            $result['data']['status'] .= ' - ' . $this->getPaymentStatusNameById($result['data']['status']);
        }
        return $result;
    }

    /**
     * Get wallet balance
     *
     * @param string $nonce
     * @return array [
     * 'status' => 'success',
     * 'data' => ['AZN' => 84519],
     * 'code' => 200,
     * 'url' => 'https://payment-api.pulpal.az/api/merchant_payment/External/balance',
     * 'message' => 'OK'
     * ]
     */
    public function getWalletBalance($nonce = '')
    {
        if (!$nonce) $nonce = time();
        $requestData = $this->prepareRequestData(self::WALLET_BALANCE_API_PATH, [], $nonce);
        $requestHeaders = $requestData['headers'];
        $requestParams = $requestData['params'];

        return $this->sendRequest(
            $this->host.self::WALLET_BALANCE_API_PATH,
            'GET',
            $requestParams,
            $requestHeaders
        );
    }

    /**
     * Get wallet balance with blocked funds
     *
     * @param string $nonce
     * @return array [
     * 'status' => 'success',
     * 'data' => [
     *      'AZN' => [
     *          'balance' => 84519,
     *          'hold' => 6400
     *      ]
     * ],
     * 'code' => 200,
     * 'url' => 'https://payment-api.pulpal.az/api/merchant_payment/External/balance-v2',
     * 'message' => 'OK'
     * ]
     */
    public function getWalletBalanceWithBlockedFunds($nonce = '')
    {
        if (!$nonce) $nonce = time();
        $requestData = $this->prepareRequestData(self::WALLET_BALANCE_V2_API_PATH, [], $nonce);
        $requestHeaders = $requestData['headers'];
        $requestParams = $requestData['params'];

        return $this->sendRequest(
            $this->host.self::WALLET_BALANCE_V2_API_PATH,
            'GET',
            $requestParams,
            $requestHeaders
        );
    }
}