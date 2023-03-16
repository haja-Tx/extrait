<?php

namespace App\Exceptions\Investment;


class ToMuchPartsNumberException extends \Exception{

    public function __construct(string $message = "Le nombre de blok doit être inferieur ou égal au nombre de blok disponible")
    {
        parent::__construct($message);
    }

}