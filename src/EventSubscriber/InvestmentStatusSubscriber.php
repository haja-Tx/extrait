<?php

namespace App\EventSubscriber;

use Monolog\DateTimeImmutable;
use App\Entity\Financial\Investment;
use App\Entity\Financial\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Financial\InvestmentService;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvestmentStatusSubscriber implements EventSubscriberInterface
{


    public function __construct(
        private InvestmentService $investmentService,
        private EntityManagerInterface $manager,
        private TranslatorInterface $translator
    ){}


    public function guardPurchase(GuardEvent $event)
    {
        /** @var Investment $investment  */
        $investment = $event->getSubject();

        if($investment->getProperty()->getStatus() != 'open'){
            $event->addTransitionBlocker(new TransitionBlocker($this->translator->trans('This property is not open for investment'), 0));
        }
        if($investment->getUser()->getStatus() != 'identity_confirmed'){
            $event->addTransitionBlocker(new TransitionBlocker($this->translator->trans('You are not allow to invest yet, please complete your profile'), 1));
        }

    }

    public function handlePurchase(TransitionEvent $event): void
    {
        /**
         * @var Investment $investment
         */
        $investment = $event->getSubject();

        $price = $this->investmentService->getPricing($investment);

        $transaction = (new Transaction())
            ->setUser($investment->getUser())
            ->setDirection(Transaction::DIRECTION_OUT)
            ->setAmount($price)
            ->setType(Transaction::TYPE_PART_PURCHASES)
            ->setStatus(Transaction::STATUS_COMPLETED)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setDescription([
                'property' => $investment->getProperty()->getTitle(),
                'part_count' => $investment->getPartCount()
            ])
        ;

        $this->manager->persist($transaction);
        $this->manager->flush();

    }

    public function guardCancel(GuardEvent $event)
    {

        /** @var Investment $investment */
        $investment = $event->getSubject();

        if($investment->getProperty()->getStatus() == 'active'){
            $event->addTransitionBlocker(new TransitionBlocker($this->translator->trans('This property is active, you cannot cancel your investment'), 1));
        }

    }

    public function handleCancel(TransitionEvent $event): void
    {

        $investment = $event->getSubject();

        $price = $this->investmentService->getPricing($investment);

        $transaction = (new Transaction())
            ->setUser($investment->getUser())
            ->setDirection(Transaction::DIRECTION_IN)
            ->setAmount($price)
            ->setType(Transaction::TYPE_FIXING_ADJUSTMENT)
            ->setStatus(Transaction::STATUS_COMPLETED)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setDescription([
                'property' => $investment->getProperty()->getTitle(),
                'part_count' => $investment->getPartCount()
            ])
        ;

        $this->manager->persist($transaction);
        $this->manager->flush();

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.investment_status.guard.buy' => 'guardPurchase',
            'workflow.investment_status.transition.buy' => 'handlePurchase',
            'workflow.investment_status.guard.cancel' => 'guardCancel',
            'workflow.investment_status.transition.cancel' => 'handleCancel'
        ];
    }
}
