# PosCredit API PHP client

## Requirements

* PHP 5.3 and above
* PHP's cURL support

## Install

1) Get [composer](https://getcomposer.org/download/)

2) Run into your project directory:
```bash
composer require nmaximenko/poscredit-api-client ~1.0.0
```

If you have not used `composer` before, include autoloader into your project.
```php
require 'path/to/vendor/autoload.php';
```

## Usage

### Get status
```php
$client = new \PosCredit\ApiClient(
    'userID',
    'userToken'
);
$id = 15707;

try {
    $responseText = $client->getCreditStatus($id);
    $response = new \PosCredit\Response\StatusResponse($responseText);
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}

if ($response->isSuccessful()) {
    echo $response->status;
    
    if ($response->status == \PosCredit\Response\StatusResponse::STATUS_DENIED) {
        echo 'credit denied';
    }

    if ($response->status >= \PosCredit\Response\StatusResponse::STATUS_AUTHORIZED) {
        $data['credit_answer'] = $response->answer;
        $data['credit_bank'] = $response->bank;
        $data['credit_sale'] = $response->sale;
        $data['credit_firstpayment'] = $response->firstPayment;
        $data['credit_dognumber'] = $response->dogNumber;
        $data['credit_creditsumm'] = $response->creditSumm;
        $data['credit_creditterms'] = $response->creditTerms;
        $data['credit_statuspayment'] = $response->transferPayment['statusPayment'];

        print_r($data);
    }
} else {
    echo $response->getError();
}
```

### Create credit request
```php

$client = new \PosCredit\ApiClient(
    'userID',
    'userToken'
);

try {
    $responseText = $client->sendCreditRequest(array(
        'request_id' => 'some-shop-order-id',
        'firstname' => 'Vasily',
        'lastname' => 'Pupkin',
        'surname' => 'Ivanovich',
        'phone' => '9612007788',
        'goods' => array(
            array(
                'name' => 'Cofe-machine',
                'price' => '26500.11',
                //'model' => 'A100',
                //'brand' => 'Nescafe',
            ),
            //...
        ),
        //'comment' => 'customer comment',
    ));
    $response = new \PosCredit\Response\ApiResponse($responseText);
} catch (\RuntimeException $e) {
    echo "Error: " . $e->getMessage();
}

if ($response->isSuccessful()) {
    echo 'Credit request successfully created. ID in PosCredit = ' . $response->idProfile;
} else {
    echo $response->getError();
}
```
