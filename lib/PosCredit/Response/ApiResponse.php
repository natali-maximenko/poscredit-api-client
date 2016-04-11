<?php

namespace PosCredit\Response;

/**
 * Базовый класс ответов API PosCredit
 *
 * @author Natali Maximenko <natali.maximenko@gmail.com>
 */
class ApiResponse {
    
    /**
     * Идентификатор анкеты
     * @var int 
     */
    public $idProfile;
    /**
     * Блок данных об ошибке. 
     * Не обязателен и может отсутствовать, если ошибок не возникло.
     */
    protected $error = false;
    /**
     * Код ошибки.
     * @var int 
     */
    protected $code;
    /**
     * Текстовое описание ошибки.
     * @var string 
     */
    protected $description;
    
    public function __construct(\SimpleXMLElement $response) {
        $body = $response->body;
        
        if (isset($body->error)) {
            $this->error = true;
            $this->code = $body->error->code->__toString();
            $this->description = $body->error->description->__toString();
        } else {
            $this->idProfile = $body->idProfile->__toString();
        }
    }
    
    public function isSuccessful() 
    {
        return !$this->error;
    }
    
    public function getError() {
        return $this->code.': '.$this->description;
    }
}
