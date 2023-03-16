<?php

namespace App\Controller;
use App\Entity\Financial\Investment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/account', name:'user_')]
class FinancialController extends AbstractController
{

    #[Route('/transactions', name:'transactions')]
    public function transations(): Response
    {


        $transactions = $this->getUser()->getTransactions();
        $withdrawalMethods = $this->getUser()->getWithdrawalMethods();

        return $this->render('financial/transactions.html.twig', [
            'transactions' => $transactions,
            'withdrawalMethods' => $withdrawalMethods
        ]);

    }

    #[Route('/investments', name:'investments')]
    public function investments(): Response
    {
        $investments = $this->getUser()->getInvestments();

        return $this->render('financial/investments.html.twig', [
            'investments' => $investments
        ]);

    }

    #[Route('/investment/cancel/{id}', name:'investment_cancel')]
    public function cancelInvestment(
        Investment $investment,
        WorkflowInterface $investmentStatusStateMachine,
        WorkflowInterface $propertyStatusStateMachine,
        EntityManagerInterface $manager
    ): Response
    {

        if($this->getUser() != $investment->getUser()){
            throw $this->createNotFoundException();
        }

        $investmentStatusStateMachine->apply($investment, 'cancel');

        $manager->remove($investment);
        
        // remettre le status du bien en open car il y encore des parts non vendus
        if($propertyStatusStateMachine->can($investment->getProperty(), 'open_purshase')){
            $propertyStatusStateMachine->apply($investment->getProperty(), 'open_purchase');
        }
        
        $manager->flush();
        
        $this->addFlash('success', "L'investissement a été annulé avec succès");

        return $this->redirectToRoute('user_investments');
    }

}
