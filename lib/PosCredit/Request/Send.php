<?php

namespace PosCredit\Request;

/**
 * Запрос на сохранение заявки на кредит
 */
class Send extends ApiRequest {
    
    /**
     * Тип запроса "Сохранение заявки на кредит"
     * @var string 
     */
    protected $operation = 'SendShortOpty';
    /**
     * Фамилия клиента
     * @var string 
     */
    private $lastname;
    /**
     * Имя клиента
     * @var string 
     */
    private $firstname;
    /**
     * Отчество клиента
     * @var string 
     */
    private $surname;
    /**
     * Номер мобильного телефона клиента
     * @var string 10 символов, пример: 9259876543
     */
    private $phone;
    /**
     * Комментарий по заявке
     * Необязательно к заполнению
     * @var string 
     */
    private $comment = '...';
    /**
     * Первоначальный взнос клиента
     * @var float 
     */
    private $summ = '0.00';
    /**
     * ID заявки на вашей стороне
     * @var string 
     */
    private $request_id;
    /**
     * Блок данных о приобретаемых товарах
     * @var array 
     */
    private $GoodsParam = array();
    
    /**
     * Заполняет данные заявки
     * @param array $data
     */
    public function createProfile($data) {
        // обязательные параметры
        $this->lastname = $data['lastname'];
        $this->firstname = $data['firstname'];
        $this->surname = $data['surname'];
        $this->phone = $data['phone'];
        
        foreach($data['goods'] as $item) {
            $param = array();
            $param['good_name'] = $item['name'];
            $param['good_price'] = $item['price'];
            if (isset($item['model']) && !empty($item['model'])) {
                $param['good_model'] = $item['model'];
            }
            if (isset($item['brand']) && !empty($item['brand'])) {
                $param['good_brand'] = $item['brand'];
            }
            
            $this->GoodsParam[] = $param;
        }
        
        // необязательные параметры
        if (isset($data['comment']) && !empty($data['comment'])) {
            $this->comment = $data['comment'];
        }
        if (isset($data['summ']) && !empty($data['summ'])) {
            $this->summ = $data['summ'];
        }
        if (isset($data['request_id']) && !empty($data['request_id'])) {
            $this->request_id = $data['request_id'];
        }
        
        return true;
    }
    
    /**
     * Сформировать тело запроса для отправки в API
     * @return xml 
     */
    public function generateRequestBody() {
        $string = '<?xml version="1.0" encoding="UTF-8"?>
            <envelope>
                <body>
                    <target>'.$this->target.'</target>
                    <operation>'.$this->operation.'</operation>
                    <userID>'.$this->userID.'</userID>
                    <userToken>'.$this->userToken.'</userToken>
                    <lastname>'.$this->lastname.'</lastname>
                    <firstname>'.$this->firstname.'</firstname>
                    <surname>'.$this->surname.'</surname>
                    <phone>'.$this->phone.'</phone>
                    <comment>'.$this->comment.'</comment>
                    <summ>'.$this->summ.'</summ>
                    <request_id>'.$this->request_id.'</request_id>
                    <GoodsParam/>
                </body>
            </envelope>
        ';
        
        $xml = new \SimpleXMLElement(
            $string,
            LIBXML_NOENT |LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE
        );
        
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($xml->asXML());
        
        $goodsParamElement = $document
            ->getElementsByTagName('GoodsParam')->item(0);
        
        foreach($this->GoodsParam as $param) {
            $articleElement = $goodsParamElement->appendChild($document->createElement('Article'));
            
            $articleElement->appendChild(
                $document->createElement('good_name', $param['good_name'])
            );
            $articleElement->appendChild(
                $document->createElement('good_price', $param['good_price'])
            );
            
            if (isset($param['good_model'])) {
                $articleElement->appendChild(
                    $document->createElement('good_model', $param['good_model'])
                );
            }
            
            if (isset($param['good_brand'])) {
                $articleElement->appendChild(
                    $document->createElement('good_brand', $param['good_brand'])
                );
            }
        }
        
        return $document->saveXML();
    }
}

