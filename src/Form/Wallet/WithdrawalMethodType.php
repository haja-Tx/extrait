<?php

namespace App\Form\Wallet;

use App\Entity\Wallet\WithdrawalMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WithdrawalMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bankName', ChoiceType::class, [
                'placeholder' => 'Choose an bank',
                'choices' => [
                    'ABC Banking Corporation Ltd' => 'ABC Banking Corporation Ltd',
                    'Absa Bank (Mauritius) Limited' => 'Absa Bank (Mauritius) Limited',
                    'AfrAsia Bank Limited' => 'AfrAsia Bank Limited',
                    'BCP Bank (Mauritius) Ltd' => 'BCP Bank (Mauritius) Ltd',
                    'Bank One Limited' => 'Bank One Limited',
                    'Bank of Baroda' => 'Bank of Baroda',
                    'Bank of China' => 'Bank of China',
                    'HSBC' => 'HSBC',
                    'Habib Bank Limited' => 'Habib Bank Limited',
                    'Investec Bank (Mauritius) Limited' => 'Investec Bank (Mauritius) Limited',
                    'MauBank Ltd' => 'MauBank Ltd',
                    'SBI (Mauritius) Ltd' => 'SBI (Mauritius) Ltd',
                    'SBM Bank (Mauritius) Ltd' => 'SBM Bank (Mauritius) Ltd',
                    'Silver Bank Limited' => 'Silver Bank Limited',
                    'Standard Bank (Mauritius) Ltd' => 'Standard Bank (Mauritius) Ltd',
                    'Standard Chartered Bank (Mauritius) Ltd' => 'Standard Chartered Bank (Mauritius) Ltd'
                ]
            ])
            ->add('bankAccountNumber')
            ->add('submit', SubmitType::class, [
                'label' => 'Save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WithdrawalMethod::class,
        ]);
    }
}
