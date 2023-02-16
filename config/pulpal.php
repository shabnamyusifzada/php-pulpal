<?php

return [

    /*
   |--------------------------------------------------------------------------
   | Host
   |--------------------------------------------------------------------------
   |
   | This option defines the environment for using API
   |
   */

    'host' => 'https://payment-api.pulpal.az',


    /*
    |--------------------------------------------------------------------------
    | Merchant ID
    |--------------------------------------------------------------------------
    |
    | This option defines the Merchant ID in PulPal system
    | You can find this parameter via merchant administration panel
    | ATTENTION!
    | Merchant ID values are different in test and production environments!
    |
    */

    'merchant_id' => 0000,

    /*
    |--------------------------------------------------------------------------
    | Provider ID
    |--------------------------------------------------------------------------
    |
    | This option defines the Provider ID for the payment
    | List of payment providers for merchant can be retrieved from PulPal support
    |
    */

    'provider_id' => 000,

    /*
    |--------------------------------------------------------------------------
    | API Public Key
    |--------------------------------------------------------------------------
    |
    | This option defines the Authentication Public Key for Top Up Payment
    | You can get this parameter by generating API Key via merchant administration panel
    |
    */

    'api_public_key' => '',

    /*
    |--------------------------------------------------------------------------
    | API Private Key
    |--------------------------------------------------------------------------
    |
    | This option defines the Authentication Private Key for Top Up Payment
    | that is used while creating signature
    | You can get this parameter by generating API Key via merchant administration panel
    |
    */

    'api_private_key' => '',

    /*
    |--------------------------------------------------------------------------
    | Lang
    |--------------------------------------------------------------------------
    |
    | Language of the payment system
    |
    */

    'lang' => 'az'
];