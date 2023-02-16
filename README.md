# PulPal Payment API for PHP
![latest release](https://img.shields.io/badge/PHP->=5.6-blue.svg?style=flat-square)
[![Latest Release on GitHub][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Based on https://payment-api-dev.pulpal.az/swagger/index.html

## About

The `php-pulpal` package allows you to accept and process payments using [PulPal API](https://payment-api-dev.pulpal.az/swagger/index.html) directly in your php application.

## Features

* Top Up Payment from the merchant
* Check status of transaction
* Check merchant wallet balance and blocked funds

## Requirements

* PHP 5.6+
* cURL

## Composer Install

Require the `shabnamyusifzada/php-pulpal` package in your `composer.json` and update your dependencies:
```sh
$ composer require shabnamyusifzada/php-pulpal
```

## Manual Install (without Composer)

- [Download](https://github.com/shabnamyusifzada/php-paypal/archive/refs/heads/main.zip) the class library and extract the contents do a directory in your project structure.
- Upload the files to your web server.

## Setup

Save config/pulpal.php to a location of your choice and fill out your details accordingly.

To use the library in your project, include the following into your file(s).

- /path/to/config.php
- autoload.php

## Integrate the package

Include the following to your file:

```php
<?php 

    require __DIR__.'/vendor/autoload.php'; 
    
    use ShabnamYusifzada\Pulpal\Api\v1\TopUpPayment;
    
    $config = include __DIR__."/config/pulpal.php";
    
    $payment = new TopUpPayment(
        $config['host'],
        $config['merchant_id'],
        $config['provider_id'],
        $config['api_public_key'],
        $config['api_private_key'],
        $config['lang']
    );
?>
```

## Using "Top Up Payment from the merchant" feature

You must initialize payment for redirecting merchant to the payment page 
using the following example: [Example](https://github.com/shabnamyusifzada/php-pulpal/blob/main/examples/top-up-payment/payment.php)

And then process the payment result using the following example: [Example](https://github.com/shabnamyusifzada/php-pulpal/examples/top-up-payment/delivery.php)

## Using "Check the status of the transaction" feature

Example right [here](https://github.com/shabnamyusifzada/php-pulpal/blob/main/examples/top-up-payment/check-status.php)

## Using "Check merchant wallet balance and blocked funds" feature

Example right [here](https://github.com/shabnamyusifzada/php-pulpal/blob/main/examples/top-up-payment/check-wallet-balance.php)

## License

Released under the MIT License, see [LICENSE](LICENSE).

[ico-version]: https://img.shields.io/github/release/shabnamyusifzada/php-pulpal.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/shabnamyusifzada/php-pulpal.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/shabnamyusifzada/php-pulpal.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/shabnamyusifzada/php-pulpal.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/shabnamyusifzada/php-pulpal
[link-scrutinizer]: https://scrutinizer-ci.com/g/shabnamyusifzada/php-pulpal/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/shabnamyusifzada/php-pulpal
[link-downloads]: https://packagist.org/packages/shabnamyusifzada/php-pulpal
[link-author]: https://github.com/shabnamyusifzada
[link-contributors]: ../../contributors