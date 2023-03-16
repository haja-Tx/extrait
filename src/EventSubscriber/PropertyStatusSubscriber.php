<?php

namespace App\EventSubscriber;
use App\Entity\Financial\Transaction;
use App\Entity\Property\Property;
use App\Entity\Financial\Investment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PropertyStatusSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private EntityManagerInterface $manager
    ){

    }

    public function handleSell(TransitionEvent $event): void
    {

        /**
         * @var Property $property
         */
        $property = $event->getSubject();

        /**
         * @var Investment[] $investments
         */
        $investments = $property->getInvestments();

        foreach($investments as $investment){

            // Valeur réel des parts au moment de l'achat = nombre de part * prix de vente d'une part
            $partValue = $investment->getPartCount() * $property->getPartSellingPrice();

            $transaction = (new Transaction())
                ->setUser($investment->getUser())
                ->setDirection(Transaction::DIRECTION_IN)
                ->setAmount($partValue)
                ->setType(Transaction::TYPE_PROPERTY_SALES)
                ->setStatus(Transaction::STATUS_COMPLETED)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setDescription([
                    'property_id' => $property->getId(),
                    'property_name' => $property->getTitle(),
                    'part_count' => $investment->getPartCount()
                ])
            ;

            $this->manager->persist($transaction);

        }

        $this->manager->flush();

    }



    public static function getSubscribedEvents(): array
    {

        return [
            'workflow.property_status.transition.sell' => 'handleSell',
        ];

    }

}