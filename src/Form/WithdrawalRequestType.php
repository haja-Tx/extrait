<?php

namespace App\Form;

use App\Entity\Wallet\WithdrawalMethod;
use App\Entity\Wallet\WithdrawalRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WithdrawalRequestType extends AbstractType
{

    public function __construct(
        private TranslatorInterface $translator
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method', EntityType::class, [
                'block_name' => 'methods_list',
                'label' => $this->translator->trans('Bank account'),
                'label_attr' => [
                    'class' => 'btn btn-outline-primary'
                ],
                'class' => WithdrawalMethod::class,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('amount', MoneyType::class, [
                'label' => $this->translator->trans('Amount'),
                'currency' => 'eur'
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('Request')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WithdrawalRequest::class,
        ]);
    }
}
