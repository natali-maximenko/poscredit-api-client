<?php

namespace PosCredit;

use PosCredit\Request\Send;
use PosCredit\Request\Status;

/**
 * Клиент для работы с API PosCredit
 *
 * @author Natali Maximenko <natali.maximenko@gmail.com>
 */
class ApiClient {
    
    /**
     * URL RestAPI
     */
    const URL = 'http://api.pos-credit.ru';
    /**
     * Методы
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    
    protected $userID;
    protected $userToken;
    protected $curl;

    /**
     * Конструктор
     * @param string $apiUser API user id
     * @param string $apiToken API user token
     */
    public function __construct($apiUser, $apiToken) 
    {
        $this->userID = $apiUser;
        $this->userToken = $apiToken;
    }
    
    /**
     * Отправить заявку на кредит
     * @param array $data данные заявки
     * @return SimpleXMLElement object
     */
    public function sendCreditRequest(array $data) 
    { 
        // формируем тело запроса
        $request = new Send($this->userID, $this->userToken);
        $request->createProfile($data);
        $body = $request->generateRequestBody();
        
        // отправляем
        return $this->curlRequest(
            self::URL,
            self::METHOD_POST,
            array('xml' => $body)
        );
    }
    
    /**
     * Получить статус заявки
     * @param int $id ID заявки
     * @return SimpleXMLElement object
     */
    public function getCreditStatus($id) 
    {
        // формируем тело запроса
        $statusRequest = new Status($this->userID, $this->userToken, $id);
        $body = $statusRequest->generateRequestBody();
        
        // отправляем
        return $this->curlRequest(
            self::URL,
            self::METHOD_POST,
            array('xml' => $body)
        );
    }
    
    /**
     * Отправка запроса через curl
     * 
     * @param string $url
     * @param string $method
     * @param array $parameters
     */
    protected function curlRequest($url, $method = 'GET', $parameters = null)
    {
        set_time_limit(0);

        if (!is_null($parameters) && $method == self::METHOD_GET) {
            $url .= $this->httpBuildQuery($parameters);
        }
        
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_FAILONERROR, false);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curlHandler, CURLOPT_POST, false);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, array());
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array());

        if ($method == self::METHOD_POST) {
            curl_setopt($curlHandler, CURLOPT_POST, true);
        }
        
        if (self::METHOD_POST === $method) {
            curl_setopt($curlHandler, CURLOPT_POST, true);
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $parameters);
        }

        if (!is_null($parameters) && $method == self::METHOD_POST &&
            isset($parameters['xml'])
        ) {
            curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/xml',
                'Accept: */*'
            ));
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $parameters['xml']);
        }

        $response = curl_exec($curlHandler);
        $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        $errno = curl_errno($curlHandler);
        $error = curl_error($curlHandler);

        if ($errno) {
            throw new RuntimeException($error, $errno);
        }

        $result = $this->getResult($response);

        if ($statusCode >= 400) {
            throw new RuntimeException($this->getError($result), $statusCode);
        }

        return $result;
    }
    
    /**
     * Сформировать ответ.
     *
     * @param string $response
     * @return SimpleXMLElement|DOMDocument
     */
    private function getResult($response)
    {
        libxml_use_internal_errors(true);
        $result = simplexml_load_string($response);

        if (!$result) {
            $document = new DOMDocument();
            $document->loadHTML($response);
            $result = $this->clearDomDocument($document);
        }

        return $result;
    }

    /**
     * Получить ошибки.
     * 
     * @param SimpleXMLElement|DOMDocument $result
     * @return string
     */
    private function getError($result)
    {
        $error = 'Internal error.';
        if ($result instanceof DOMDocument) {
            $error = strip_tags($result->textContent);
        } elseif (count($result->message) > 0) {
            $error = '';
            foreach ($result->message as $message) {
                $error .= "{$message}\n";
            }
        }

        return $error;
    }

}
