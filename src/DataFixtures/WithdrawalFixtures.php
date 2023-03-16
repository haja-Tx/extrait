<?php

namespace App\DataFixtures;

use App\Entity\Security\User;
use App\Entity\Financial\Transaction;
use App\Entity\Wallet\WithdrawalMethod;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Wallet\WithdrawalRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;

class WithdrawalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user = $manager->getRepository(User::class)->findOneBy([
            'email' => 'admin@blok.mu'
        ]);

        $withdrawalMethod = (new WithdrawalMethod())
            ->setBankName('Habib Bank Limited')
            ->setBankAccountNumber('FR 76 285219 6614 2334 508845 0 09')
            ->setUser($user)
        ;
        
        $transaction = (new Transaction())
            ->setAmount(100)
            ->setDescription([
                    'bank_name' => $withdrawalMethod->getBankName(),
                    'bank_account_number' => $withdrawalMethod->getBankAccountNumber(),
            ])
            ->setDirection(Transaction::DIRECTION_OUT)
            ->setStatus(Transaction::STATUS_COMPLETED)
            ->setType(Transaction::TYPE_WITHDRAWAL)
            ->setUser($user)
        ;
                    
        $withdrawalRequest = (new WithdrawalRequest())
            ->setAmount(100)
            ->setMethod($withdrawalMethod)
            ->setStatus('payed')
            ->setTransaction($transaction)
            ->setUser($user)
        ;

        $manager->persist($withdrawalMethod);
        $manager->persist($withdrawalRequest);
        $manager->persist($transaction);

        $manager->flush();
    }
}
