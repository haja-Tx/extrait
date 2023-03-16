<?php

namespace App\Form;

use App\Form\Type\GenderType;
use App\Entity\Security\Detail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class UserDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('gender', GenderType::class)
            ->add('birthday',DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('country', CountryType::class,
            [
                'required' => true,
            ])
            ->add('city', TextType::class,  
            [
                'required' => true,
            ])
            ->add('postal_code')
            ->add('address')
            ->add('phone')
            // ->add('reference')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detail::class,
        ]);
    }
}
