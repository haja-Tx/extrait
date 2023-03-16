<?php

namespace App\Controller;

use App\Form\WithdrawalRequestType;
use App\Entity\Wallet\WithdrawalMethod;
use App\Entity\Wallet\WithdrawalRequest;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Wallet\WithdrawalMethodType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/account/withdrawal-request', name:'withdrawal_request_')]
class WithdrawalRequestController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function index(Request $request, EntityManagerInterface $manager, WorkflowInterface $withdrawalStatusStateMachine): Response
    {

        $withdrawalRequest = (new WithdrawalRequest())
            ->setUser($this->getUser())
        ;

        $requestForm = $this->createForm(WithdrawalRequestType::class, $withdrawalRequest);
        $requestForm->handleRequest($request);
        if($requestForm->isSubmitted() && $requestForm->isValid()){
            $withdrawalStatusStateMachine->apply($withdrawalRequest, 'start');
            $manager->persist($withdrawalRequest);
            $manager->flush();

            $this->addFlash('success', 'Withdrawal request saved successfully');

            return $this->redirectToRoute('withdrawal_request_add');
            
        }

        $withdrawalMethod = (new WithdrawalMethod())
            ->setUser($this->getUser())
        ;

        $methodForm = $this->createForm(WithdrawalMethodType::class, $withdrawalMethod);
        $methodForm->handleRequest($request);

        if($methodForm->isSubmitted() && $methodForm->isValid()){

            $manager->persist($withdrawalMethod);
            $manager->flush();
            $this->addFlash('success', 'Withdrawal method saved successfully');

            return $this->redirectToRoute('withdrawal_request_add');

        }


        return $this->render('withdrawal_request/add.html.twig', [
            'request_form' => $requestForm->createView(),
            'method_form' => $methodForm->createView()
        ]);
    }
}
