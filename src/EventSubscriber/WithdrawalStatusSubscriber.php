<?php

namespace App\EventSubscriber;


use App\Entity\Financial\Transaction;
use App\Entity\Wallet\WithdrawalRequest;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class WithdrawalStatusSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private EntityManagerInterface $manager
    ){}

    public function handleStart(TransitionEvent $event){

        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $event->getSubject();

        $transaction = (new Transaction())
            ->setUser($withdrawalRequest->getUser())
            ->setAmount($withdrawalRequest->getAmount())
            ->setType(Transaction::TYPE_WITHDRAWAL)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setDirection(Transaction::DIRECTION_OUT)
            ->setStatus(Transaction::STATUS_PENDING)
            ->setDescription([
                'bank_name' => $withdrawalRequest->getMethod()->getBankName(),
                'bank_account_number' => $withdrawalRequest->getMethod()->getBankAccountNumber(),
            ])
        ;

        $withdrawalRequest->setTransaction($transaction);

        $this->manager->persist($transaction);
        
    }

    public function handleReject(TransitionEvent $event){
        
        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $event->getSubject();

        $transaction = $withdrawalRequest->getTransaction();
        
        $transaction->setStatus(Transaction::STATUS_CANCELED);
        $this->manager->persist($transaction);
        $this->manager->flush();
        
    }
    
    public function handlePay(TransitionEvent $event){
        
        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $event->getSubject();
        
        $transaction = $withdrawalRequest->getTransaction();
        
        $transaction->setStatus(Transaction::STATUS_COMPLETED);
        $this->manager->persist($transaction);
        $this->manager->flush();

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.withdrawal_status.transition.start' => 'handleStart',
            'workflow.withdrawal_status.transition.reject' => 'handleReject',
            'workflow.withdrawal_status.transition.pay' => 'handlePay',
        ];
    }

}