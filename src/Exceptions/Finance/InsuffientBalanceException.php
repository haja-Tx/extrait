<?php

namespace App\Exceptions\Finance;

class InsuffientBalanceException extends \Exception{

    public function __construct(string $message = "Votre solde est insuffisant pour cette action")
    {
        parent::__construct($message);
    }

}