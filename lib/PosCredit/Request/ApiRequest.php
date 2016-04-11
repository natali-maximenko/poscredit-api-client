<?php

namespace PosCredit\Request;

/**
 * Базовый класс запросов к API PosCredit
 *
 * @author Natali Maximenko <natali.maximenko@gmail.com>
 */
class ApiRequest {
    
    protected $target = 'API';
    
    /**
     * Тип запроса
     * @var string 
     */
    protected $operation;
    /**
     * Код партнера
     * @var sting 
     */
    protected $userID;
    /**
     * Хэш-пароль партнера
     * @var string 
     */
    protected $userToken;

    /**
     * Конструктор
     * @param string $user
     * @param string $token
     */
    public function __construct($user, $token) {
        
        $this->userID = $user;
        $this->userToken = $token;
    }
}
