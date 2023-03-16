<?php

namespace App\DataFixtures;

use App\Entity\Security\User;
use App\Entity\Financial\Transaction;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TransactionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $transaction = (new Transaction())
            ->setAmount(10000)
            ->setDirection(Transaction::DIRECTION_IN)
            ->setStatus('completed')
            ->setType(Transaction::TYPE_DEPOSIT)
            ->setUser($manager->getRepository(User::class)->findOneBy([
                'email' => 'admin@blok.mu'
            ]))
        ;

        $manager->persist($transaction);
        $manager->flush();
    }
}
