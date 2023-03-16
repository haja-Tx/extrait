<?php

namespace App\Service\Financial;

use App\Entity\Security\User;
use App\Entity\Financial\Transaction;
use Doctrine\ORM\EntityManagerInterface;


class WalletService{

    public function __construct(
        private EntityManagerInterface $manager
    ){}


    public function computeBalance(User $user): float {


        /**
         * @var Transaction[] $transactions
         */
        $transactions = $user->getTransactions();

        $balance = 0;

        foreach($transactions as $transaction){

            if($transaction->getStatus() != Transaction::STATUS_CANCELED){
                $amount = $transaction->getAmount();
                $direction = $transaction->getDirection();
    
                if($direction == Transaction::DIRECTION_IN){
                    $balance += $amount;
                }else if($direction == Transaction::DIRECTION_OUT){
                    $balance -= $amount;
                }
            }
            
        }

        return $balance;

    }

}