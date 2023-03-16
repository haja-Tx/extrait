<?php

namespace App\Controller\Admin;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Workflow\WorkflowInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureActions(Actions $actions): Actions
    {

        $acceptAction = Action::new('accept_documents', 'Accept documents', 'fa fa-check')
            ->linkToCrudAction('acceptDocuments')
            ->displayIf(function($user){
                return $user->getStatus() == 'pending';
            })
            ->setCssClass('btn btn-success')
        ;

        $rejectAction = Action::new('reject_documents', 'Reject documents', 'fa fa-close')
            ->linkToCrudAction('rejectDocuments')
            ->displayIf(function($user){
                return $user->getStatus() == 'pending';
            })
            ->setCssClass('btn btn-danger')
        ;

        return $actions
            ->add(Crud::PAGE_DETAIL, $acceptAction)    
            ->add(Crud::PAGE_DETAIL, $rejectAction)
            ->remove(Crud::PAGE_INDEX, Action::EDIT) 
            ->add(Crud::PAGE_INDEX, Action::DETAIL) 
        ;

    }
    


    public function configureFields(string $pageName): iterable
    {
        return [
            // Onglet détail 
            FormField::addTab('informations genérales'),
            // ImageField::new('userFiles.imageName', 'Avatar')
            //     ->setBasePath($this->getParameter("app.path.thumbnail_images"))
            //     ->hideOnForm()
            // ,
            EmailField::new('email'),
            TextField::new('lastname', 'Nom'),
            TextField::new('firstname', 'Prénom'),
            TextField::new('status'),
            // ,
            // CollectionField::new('investments')
            //     ->onlyOnIndex()
            // ,
            FormField::addTab('Documents'),
            ImageField::new('files.frontIdCardName', 'Identity card (Recto)')
                ->setBasePath($this->getParameter("app.path.identity_files"))
                ->hideOnForm()
                ->hideOnIndex()
            ,
            ImageField::new('files.backIdCardName', 'Identity card (Verso)')
                ->setBasePath($this->getParameter("app.path.identity_files"))
                ->hideOnForm()
                ->hideOnIndex()
            ,
            ImageField::new('files.proofOfAddressName', 'Proof of address')
                ->setBasePath($this->getParameter("app.path.identity_files"))
                ->hideOnForm()
                ->hideOnIndex()
            ,
            // Onglet détail 
            FormField::addTab('Détail'),
            // MoneyField::new('defaultWallet.amount', 'Solde')
            //     ->hideOnIndex()
            //     ->setCustomOption(MoneyField::OPTION_CURRENCY, "EUR")
            // ,
            // TextField::new('address', 'Adresse')
            //     ->hideOnIndex()
            // ,
            // TextField::new('city', 'Adresse')
            //     ->hideOnIndex()
            // ,
            // TextField::new('address', 'Adresse')
            //     ->hideOnIndex()
            // ,
            // DateField::new('birthday', 'Date de naissance')
            //     ->hideOnIndex()
            // ,

        ];
    }


    public function acceptDocuments(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        WorkflowInterface $userStatusStateMachine,
        EntityManagerInterface $manager
    ): Response {

        /** @var User $user */
        $user = $context->getEntity()->getInstance();

        $userStatusStateMachine->apply($user, 'confirm_identity');

        $this->addFlash('success', 'User documents accepted successfully');


        $manager->persist($user);
        $manager->flush();

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);

    }

    public function rejectDocuments(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        WorkflowInterface $userStatusStateMachine,
        EntityManagerInterface $manager
    ): Response {

        /** @var User $user */
        $user = $context->getEntity()->getInstance();

        $userStatusStateMachine->apply($user, 'reject_identity');

        $this->addFlash('success', 'User documents rejected successfully');


        $manager->persist($user);
        $manager->flush();

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);

    }


}
