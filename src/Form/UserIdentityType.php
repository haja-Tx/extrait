<?php

namespace App\Form;

use App\Entity\Security\Files;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserIdentityType extends AbstractType
{

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {

        $this->translator = $translator;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('frontIdCard', VichImageType::class, [
                'label' => "Front identity Card",
                'attr' => [
                    'id' => 'identity-front',
                    'required' => true
                ],
                'allow_delete' => false,
                'download_link' => false,
                'image_uri' => false,

            ])
            ->add('backIdCard', VichImageType::class, [
                'label' => "Back of identityCard",
                'attr' => [
                    'id' => 'identity-back',
                    'required' => false
                ],
                'allow_delete' => false,
                'download_link' => false,
                'image_uri' => false,

            ])
            ->add('proofOfAddress', VichImageType::class, [
                'label' => "Back of identityCard",
                'attr' => [
                    'id' => 'proof-of-address',
                    'required' => false
                ],
                'allow_delete' => false,
                'download_link' => false,
                'image_uri' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('Submit')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
        ]);
    }
}
