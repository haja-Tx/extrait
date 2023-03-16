<?php

namespace App\Form;

use App\Entity\Financial\Investment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuyPartType extends AbstractType
{

    public function __construct(
        private TranslatorInterface $translator
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partCount', IntegerType::class,[
                'label' => $this->translator->trans('partNumer'),
                'constraints' => [
                    
                ]
            ])
            ->add('buy', SubmitType::class, [
                'label' => $this->translator->trans('buy')
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Investment::class,
        ]);
    }
}
