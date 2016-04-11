<?php

namespace PosCredit\Response;

/**
 * Ответ PosCredit на запрос о статусе заявки
 *
 * @author natalia
 */
class StatusResponse extends ApiResponse {
    
    // Статусы решения по заявке. 
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_DENIED = 2;
    const STATUS_GRANTED = 3;
    const STATUS_ERROR = 4;
    const STATUS_CLIENT_CANCEL = 5;
    const STATUS_AUTHORIZED = 6;
    const STATUS_CONTRACT_SIGNED = 7;
    // Статус оплаты по кредиту
    const PAYMENT_APPROVED = 'approved';

    /**
     * Решение по заявке. 0 - новая заявка, 1 - в обработке, 2 - в кредите отказано, 
     * 3 - кредит предоставлен, 4 - ошибочный ввод, 5 - отказ клиента, 
     * 6 - договор авторизован, 7 - договор подписан
     * @var int 
     */
    public $status;
    /**
     * Комментарий при обработке заявки
     * @var string 
     */
    public $answer;
    /**
     * Если status >= 3, то передается значение банка, в котором предоставлен кредит. 
     * 1 - ОТП БАНК, 2 - РЕНЕССАНС БАНК, 3 - РУСФИНАНС БАНК, 4 - РУССКИЙ СТАНДАРТ БАНК, 
     * 5 - Восточный Экспресс Банк, 6 - Альфа Банк, 7 - Лето Банк
     * @var int 
     */
    public $bank;
    /**
     * Если status=3, то передается договор в формате Base64 в виде строки
     * @var string 
     */
    public $dogovor;
    /**
     * Процент скидки для клиента при рассрочке
     * @var float 
     */
    public $sale;
    /**
     * Первоначальный взнос
     * @var float
     */
    public $firstPayment;
    /**
     * Номер банковского договора
     * @var string 
     */
    public $dogNumber;
    /**
     * Сумма кредита в рублях
     * @var float 
     */
    public $creditSumm;
    /**
     * Срок кредита в месяцах
     * @var int 
     */
    public $creditTerms;
    /**
     * Блок данных по отправлению платежа за кредит от POS-Credit к Партнеру
     * @var array 
     */
    public $transferPayment;
    
    
    public function __construct(\SimpleXMLElement $response) {
        parent::__construct($response);
        
        $body = $response->body;
        // заполняем данные о статусе
        $this->idProfile = $body->idProfile->__toString();
        $this->status = $body->status->__toString();
        $this->answer = $body->answer->__toString();
        $this->bank = $body->bank->__toString();
        $this->dogovor = $body->dogovor->__toString();
        $this->sale = $body->sale->__toString();
        $this->firstPayment = $body->firstPayment->__toString();
        $this->dogNumber = $body->dogNumber->__toString();
        $this->creditSumm = $body->creditSumm->__toString();
        $this->creditTerms = $body->creditTerms->__toString();
        
        // если договор авторизован
        if ($this->status >= StatusResponse::STATUS_AUTHORIZED) {
            // заполняем данные о переводе денег
            foreach ($body->transferPayment[0] as $param) {
                $paramName = $param->getName();
                $this->transferPayment[$paramName] = $param->__toString();
            }
        }
    }
}
