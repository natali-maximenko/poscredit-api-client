<?php

namespace PosCredit\Request;

/**
 * Запрос статуса заявки на кредит
 */
class Status extends ApiRequest {
    
    /**
     * Тип запроса "Статус заявки на кредит"
     * @var string 
     */
    protected $operation = 'StatusShortOpty';
    
    /**
     * Идентификатор анкеты
     * @var int 
     */
    private $idProfile;
    
    /**
     * Конструктор
     * @param string $user
     * @param string $token
     * @param int $idProfile ID заявки
     */
    public function __construct($user, $token, $idProfile) {
        parent::__construct($user, $token);
        $this->idProfile = $idProfile;
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
                    <idProfile>'.$this->idProfile.'</idProfile>
                </body>
            </envelope>
        ';
        
        $xml = new \SimpleXMLElement(
            $string,
            LIBXML_NOENT |LIBXML_NOCDATA | LIBXML_COMPACT | LIBXML_PARSEHUGE
        );
        
        return $xml->asXML();
    }
}


