# PHP-клиент для PosCredit API

## Обязательные требования

* PHP версии 5.3 и выше
* PHP-расширение cURL

## Установка

1) Установите [composer](https://getcomposer.org/download/)

2) Выполните в папке проекта:
```bash
composer require nmaximenko/poscredit-api-client dev-master
```

В конфиг `composer.json` вашего проекта будет добавлена библиотека `nmaximenko/poscredit-api-client`, которая установится в папку `vendor/`. При отсутствии файла конфига или папки с вендорами они будут созданы.

В случае, если до этого в вашем проекте не использовался `composer`, подключите файл автозагрузки вендоров. Для этого укажите в коде проекта:
```php
require 'path/to/vendor/autoload.php';
```

## Примеры использования

### Получение информации о статусе заявки на кредит
```php
$client = new \PosCredit\ApiClient(
    'userID',
    'userToken'
);
$id = 'pocsredit-request-id';

try {
    $responseText = $client->getCreditStatus($id);
    $response = new \PosCredit\Response\StatusResponse($responseText);
} catch (\RuntimeException $e) {
    echo "Ошибки: " . $e->getMessage();
}

if ($response->isSuccessful()) {
    echo $response->status;
    
    if ($response->status == \PosCredit\Response\StatusResponse::STATUS_DENIED) {
        echo 'В кредите отказано';
    }

    // если договор авторизован или подписан
    if ($response->status >= \PosCredit\Response\StatusResponse::STATUS_AUTHORIZED) {
        $data['credit_answer'] = $response->answer;
        $data['credit_bank'] = $response->bank;
        $data['credit_sale'] = $response->sale;
        $data['credit_firstpayment'] = $response->firstPayment;
        $data['credit_dognumber'] = $response->dogNumber;
        $data['credit_creditsumm'] = $response->creditSumm;
        $data['credit_creditterms'] = $response->creditTerms;

        // если есть информация по отправлению платежа за кредит от POS-Credit к Партнеру
        if (!empty($response->transferPayment)) {
            if (!empty($response->transferPayment['datePayment'])) {
                $data['credit_datepayment'] = $response->transferPayment['datePayment'];
            }

            if (!empty($response->transferPayment['statusPayment'])) {
                $data['credit_statuspayment'] = $response->transferPayment['statusPayment'];
            }
        }

        // информация по предоставленному кредиту
        print_r($data);
    }
} else {
    echo $response->getError();
}
```

### Создание заявки на кредит
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
    echo 'Заявка на кредит успешно создана. ID заявки в PosCredit = ' . $response->idProfile;
} else {
    echo $response->getError();
}
```
