<?php

namespace App\Twig\Extension;

use Twig\TwigFunction;
use App\Entity\Property\Property;
use App\Entity\Financial\Investment;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\PersistentCollection;
use App\Service\Financial\InvestmentService;

class PropertyExtension extends AbstractExtension
{

    public function __construct(
        private InvestmentService $service
    ){}

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            // new TwigFilter('format_transaction_type', [$this, 'formatTransactionType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('property_available_part', [$this, 'availablePart']),
            new TwigFunction('property_sold_part', [$this, 'soldPart']),
        ];
    }

    public function availablePart(Property $property, PersistentCollection $investments): int
    {

        $initialParts = $this->service->getPartNumber($property);

        $soldParts = 0;

        /**
         * @var $investment Investment
         */ 
        foreach($investments as $investment){
            $soldParts += $investment->getPartCount();
        }

        return $initialParts - $soldParts;

    }

    public function soldPart(Property $property, PersistentCollection $investments): int
    {
        return $this->service->getPartNumber($property) - $this->availablePart($property, $investments);
    }

}
