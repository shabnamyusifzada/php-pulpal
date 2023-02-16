<?php
const INSUFFICIENT_FUNDS_PAGE = '';
const SUCCESSFUL_PAYMENT_PAGE = '';
$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);
$logPath = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . date('Y-m-d') . '-delivery-user-payments.log';
$FullDateTime = date("d-m-Y H:i:s");
$line = '';
$headers = [];

foreach (getallheaders() as $name => $value) {
    $line .= "$name: $value\n";
    $headers[$name] = $value;
}

$headers = json_encode($headers);

file_put_contents($logPath, $headers . $requestBody . "\r\n\r\n", FILE_APPEND);

//find from the transactions table data with the corresponding PaymentExternalId and PaymentAttempt and status = 0
$paymentRequest = ['id' => 1, 'expected_amount' => 100, 'payed_amount' => null];

if ($paymentRequest['id']) {
    $payedAmount = $requestData['Price'];
    $expectedAmount = $paymentRequest['expected_amount'];

    if ($payedAmount != $expectedAmount) {
        //Payed amount does not correspond to expected amount. Save it in db
        file_put_contents($logPath, $FullDateTime . "| | Transaction #" . $paymentRequest['id'] . ": Payed amount " . $payedAmount . " does not correspond to expected amount " . $expectedAmount . "\r\n\r\n", FILE_APPEND);

        header("Location: " . INSUFFICIENT_FUNDS_PAGE);
        exit;
    }
    //save successful delivery in db
    file_put_contents($logPath, $FullDateTime . "| | Transaction #" . $paymentRequest['id'] . " is successful \r\n\r\n", FILE_APPEND);
    header("Location: " . SUCCESSFUL_PAYMENT_PAGE);
}
