<?php

namespace App\Twig\Extension;

use Twig\TwigFilter;
use App\Entity\Financial\Transaction;
use Twig\Extension\AbstractExtension;

class TransactionFormatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('format_transaction_type', [$this, 'formatTransactionType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            // new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function formatTransactionType($value, $locale='en')
    {
        if($locale == 'fr'){
            switch ($value){
    
                case Transaction::TYPE_DEPOSIT:
                    return 'Dépot';
                case Transaction::TYPE_WITHDRAWAL:
                    return 'Retrait';
                case Transaction::TYPE_PART_PURCHASES:
                    return 'Achat de blok';
                case Transaction::TYPE_REVENUE:
                    return 'Revenue sur investissement';
                case Transaction::TYPE_PART_SALES:
                    return 'Vente de blok';
                case Transaction::TYPE_FIXING_ADJUSTMENT:
                    return 'Ajustement correctif';
            }
        } else if($locale == 'en'){
            switch ($value){
    
                case Transaction::TYPE_DEPOSIT:
                    return 'Deposit';
                case Transaction::TYPE_WITHDRAWAL:
                    return 'Withdrawal';
                case Transaction::TYPE_PART_PURCHASES:
                    return 'Blok purchase';
                case Transaction::TYPE_REVENUE:
                    return 'Return on investment';
                case Transaction::TYPE_PART_SALES:
                    return 'Blok sale';
                case Transaction::TYPE_FIXING_ADJUSTMENT:
                    return 'Fixing adjustment';
                case Transaction::TYPE_PROPERTY_SALES:
                        return 'Property sales gain';
            }
        }
    }
}
