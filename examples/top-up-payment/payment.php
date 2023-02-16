<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/*
 * PulPal API Integration example for PHP
 *
 * More information can be found at https://payment-api-dev.pulpal.az/swagger/index.html
 *
 */

/*
 * Account information. Below is all the variables needed to perform a purchase with
 * payson. Replace the placeholders with your actual information
 */

require __DIR__ . '/../../vendor/autoload.php';

use ShabnamYusifzada\Pulpal\Api\v1\TopUpPayment;


$config = include __DIR__."/../../config/pulpal.php";

$payment = new TopUpPayment(
    $config['host'],
    $config['merchant_id'],
    $config['provider_id'],
    $config['api_public_key'],
    $config['api_private_key'],
    $config['lang']
);


//product unique id in your system
$productExternalId = time() . uniqid();

// For example, for product with price 1 AZN you must pass 100 AZN
$productPrice = 100;

//product unique code in merchant system if exists
$productUniqueCode = isset($_GET['product_unique_code']) ? $_GET['product_unique_code'] : false;

$cardHolder = 'Shabnam Yusifzada';

$response = array('status' => 'error', 'message' => 'Unexpected response');

$payment->setProductPrice($productPrice);

/*  Generating product. If the product is already created in the merchant system and you have its unique code,
    then just get its data, otherwise create product in merchant system
*/
if ($productUniqueCode) {
    $payment->setProductUniqueCode($productUniqueCode);
    $generatedProductApiResponse = $payment->getProductByUniqueCode($productUniqueCode);
} else {
    $productNames = [
        'az' => 'Test product name az',
        'ru' => 'Test product name ru',
        'en' => 'Test product name en'
    ];
    $productDescriptions = [
        'az' => 'Test product description az',
        'ru' => 'Test product description ru',
        'en' => 'Test product description en'
    ];
    $productPrices = [
        'AZN' => $productPrice,
    ];

    $generatedProductApiResponse = $payment->generateProduct($productNames, $productDescriptions, $productPrices, $productExternalId);
}

if (
    isset($generatedProductApiResponse['status'])
    && $generatedProductApiResponse['status'] === 'success'
) {
    $product = $generatedProductApiResponse['data'];
    if (isset($product['id'])) {
        $payment->setProductId($product['id']);
        if (!$productUniqueCode) {
            $productUniqueCode = $payment->setProductUniqueCode($product['uniqueCode']);
        }

        $externalPaymentId = uniqid() . time();

        // Initialize payment and redirect to the payment page
        $initializedPaymentApiResponse = $payment->initializePayment($externalPaymentId, $cardHolder);

        if (
            isset($initializedPaymentApiResponse['status'])
            && $initializedPaymentApiResponse['status'] === 'success'
        ) {
            $data = $initializedPaymentApiResponse['data'];
            $paymentAttempt = $data['paymentAttempt'];
            $url = $data['redirectUrl'];

            //add to db initialized transaction data
            //redirects to the payment page
            header("Location: " . $url);
        } else {
            //display response if it is not successful
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/' . date('Y-m-d') . '-pulpal-user-payments.log', json_encode($initializedPaymentApiResponse) . "\r\n\r\n", FILE_APPEND);

            echo '<pre>';
            print_r($initializedPaymentApiResponse);
            echo '</pre>';
        }
        exit;
    }
    $response['message'] = 'Product is not created';
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/' . date('Y-m-d') . '-pulpal-user-payments.log', $response['message'] . "\r\n\r\n", FILE_APPEND);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    $response = array_merge($response, $generatedProductApiResponse);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

exit;
