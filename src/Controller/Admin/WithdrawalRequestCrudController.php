<?php

namespace App\Controller\Admin;

use App\Entity\Wallet\WithdrawalRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Workflow\WorkflowInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WithdrawalRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WithdrawalRequest::class;
    }

    public function configureActions(Actions $actions): Actions
    {

        $acceptAction = Action::new('accept_withdrawal', 'Set as payed', 'fa fa-check')
            ->linkToCrudAction('acceptWithdrawal')
            ->displayIf(function($withdrawalRequest){
                return $withdrawalRequest->getStatus() == 'pending';
            })
            ->setCssClass('btn btn-success')
        ;

        $rejectAction = Action::new('reject_withdrawal', 'Reject withdrawal', 'fa fa-close')
            ->linkToCrudAction('rejectWithdrawal')
            ->displayIf(function($withdrawalRequest){
                return $withdrawalRequest->getStatus() == 'pending';
            })
            ->setCssClass('btn btn-danger')
        ;


        return $actions
            ->add(Crud::PAGE_INDEX, $rejectAction)
            ->add(Crud::PAGE_DETAIL, $rejectAction)
            ->add(Crud::PAGE_INDEX, $acceptAction)
            ->add(Crud::PAGE_DETAIL, $acceptAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        ;

    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('user.email', 'Email'),
            MoneyField::new('amount')
                ->setCustomOption(MoneyField::OPTION_CURRENCY, 'EUR')
                ->setCustomOption(MoneyField::OPTION_STORED_AS_CENTS, false)
            ,
            TextField::new('method.bankAccountNumber', 'Account number'),
            TextField::new('method.bankName', 'Bank Name'),
            DateTimeField::new('createdAt')
                ->hideOnIndex()
            ,
            TextField::new('status'),
        ];
    }

    public function acceptWithdrawal(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        WorkflowInterface $withdrawalStatusStateMachine,
        EntityManagerInterface $manager
    ): Response {

        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $context->getEntity()->getInstance();

        $withdrawalStatusStateMachine->apply($withdrawalRequest, 'pay');

        $this->addFlash('success', 'Withdrawal request payed successfully');

        $manager->persist($withdrawalRequest);
        $manager->flush();

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);

    }

    public function rejectWithdrawal(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        WorkflowInterface $withdrawalStatusStateMachine,
        EntityManagerInterface $manager
    ): Response {

        /** @var WithdrawalRequest $withdrawalRequest */
        $withdrawalRequest = $context->getEntity()->getInstance();

        $withdrawalStatusStateMachine->apply($withdrawalRequest, 'reject');

        $this->addFlash('success', 'Withdrawal request rejected successfully');


        $manager->persist($withdrawalRequest);
        $manager->flush();

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);

    }


}
