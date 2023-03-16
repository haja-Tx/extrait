<?php

namespace App\Controller;

use App\Entity\Wallet\WithdrawalMethod;
use App\Form\Wallet\WithdrawalMethodType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Wallet\WithdrawalMethodRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

#[Route('/account/withdrawal-method', name:'withdrawal_method_')]
class WithdrawalMethodController extends AbstractController
{

    public function __construct(
        private TranslatorInterface $translator
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(WithdrawalMethodRepository $withdrawalMethodRepository): Response
    {

        $withdrawalMethods = $withdrawalMethodRepository->findByUser($this->getUser());

        return $this->render('withdrawal_method/index.html.twig', [
            'withdrawal_methods' => $withdrawalMethods,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, WithdrawalMethodRepository $withdrawalMethodRepository): Response
    {
        $withdrawalMethod = (new WithdrawalMethod())
            ->setUser($this->getUser())
        ;

        $form = $this->createForm(WithdrawalMethodType::class, $withdrawalMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $withdrawalMethodRepository->save($withdrawalMethod, true);

            return $this->redirectToRoute('app_withdrawal_method_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('withdrawal_method/new.html.twig', [
            'withdrawal_method' => $withdrawalMethod,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WithdrawalMethod $withdrawalMethod, WithdrawalMethodRepository $withdrawalMethodRepository): Response
    {
        $form = $this->createForm(WithdrawalMethodType::class, $withdrawalMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $withdrawalMethodRepository->save($withdrawalMethod, true);

            return $this->redirectToRoute('app_withdrawal_method_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('withdrawal_method/edit.html.twig', [
            'withdrawal_method' => $withdrawalMethod,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(WithdrawalMethod $withdrawalMethod, WithdrawalMethodRepository $withdrawalMethodRepository): Response
    {
        if ($withdrawalMethod->getUser() == $this->getUser()) {

            // On verifie avant de supprimer s'il y a encore des requetes associees
            $requests = $withdrawalMethod->getWithdrawalRequests();
            $usedMethod = false;
            foreach($requests as $withdrawalRequest){
                if($withdrawalRequest->getStatus() == 'pending'){
                    $usedMethod = true;
                    break;
                }
            }
            if(!$usedMethod){
                $withdrawalMethodRepository->remove($withdrawalMethod, true);
                $this->addFlash('success', $this->translator->trans('Withdrawal method deleted successfully'));
            }else{
                $this->addFlash('error', 'You can not yet remove this withdrawal method because there is still a request linked to it');
            }

        }else{
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('user_transactions', [], Response::HTTP_SEE_OTHER);
    }
}
