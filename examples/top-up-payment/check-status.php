<?php
require __DIR__ . '/../../vendor/autoload.php';

use ShabnamYusifzada\Pulpal\Api\v1\TopUpPayment;

if(!isset($_GET['external_id'])) die('Payment External ID is required');

$config = include __DIR__."/../../config/pulpal.php";

$payment = new TopUpPayment(
    $config['host'],
    $config['merchant_id'],
    $config['provider_id'],
    $config['api_public_key'],
    $config['api_private_key'],
    $config['lang']
);

$data = [
    'externalId' => $_GET['external_id']
];
$result = $payment->getStatusByPaymentExternalId($data);
if (isset($_GET['pretty'])) {
    echo '<pre>';
    print_r($result['data']);
    echo '</pre>';
} else {
    echo json_encode($result);
}