<?php


namespace App\Service\Financial;

use Doctrine\ORM\EntityManager;
use App\Entity\Property\Property;
use App\Entity\Financial\Investment;
use App\Service\Financial\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use App\Exceptions\Finance\InsuffientBalanceException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Exceptions\Investment\ToMuchPartsNumberException;


class InvestmentService{


    public function __construct(
        private EntityManagerInterface $manager,
        private TranslatorInterface $translator,
        private WalletService $walletService
    ){}


    public function getPartNumber(Property $property)
    {
        
        $initialParts = $property->getEstimatePrice() / $property->getPartPrice();

        return $initialParts;

    }

    /**
     * Calcul le nombre de brique disponible pour une propriété
     */
    public function getAvailableParts(Property $property): int
    {

        $investments = $property->getInvestments();

        $initialParts = $this->getPartNumber($property);

        $soldParts = 0;

        /**
         * @var $investment Investment
         */ 
        foreach($investments as $investment){
            $soldParts += $investment->getPartCount();
        }

        return $initialParts - $soldParts;

    }

    public function checkInvestment(Investment $investment){

        $user = $investment->getUser();
        $property = $investment->getProperty();

        $partsAmount = $this->getPricing($investment);

        if($this->walletService->computeBalance($user) < $partsAmount)
        {
            throw new InsuffientBalanceException($this->translator->trans("Votre solde est insuffisant pour cette action"));
        }

        if($this->getAvailableParts($property) < $investment->getPartCount())
        {
            throw new ToMuchPartsNumberException($this->translator->trans("Le nombre de brique doit être inferieur ou égal au nombre de brique disponible"));
        }

        return true;

    }

    public function saveInvestment(Investment $investment){

        $repository = $this->manager->getRepository(Investment::class);

        /** @var Investment $existingInvestment */
        $existingInvestment = $repository->findOneBy([
            'user' => $investment->getUser(),
            'property' => $investment->getProperty()
        ]);

        if(!is_null($existingInvestment)){
            $existingInvestment->increasePartsNumber($investment->getPartCount());
            $this->manager->persist($existingInvestment);
        } else {
            $this->manager->persist($investment);
        }
        
        $this->manager->flush();

    }

    public function getPricing(Investment $investment){
        /**
         * @var $property Property
         */ 
        $property = $investment->getProperty();
        return $property->getPartPrice() * $investment->getPartCount();
    }

}