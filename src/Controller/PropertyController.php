<?php

namespace App\Controller;

use App\Form\BuyPartType;
use App\Entity\Property\Property;
use App\Entity\Financial\Investment;
use Doctrine\ORM\EntityManagerInterface;
use Hashids\HashidsInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\Financial\InvestmentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\Property\PropertyRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Financial\InvestmentRepository;
use App\Exceptions\Finance\InsuffientBalanceException;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Exceptions\Investment\ToMuchPartsNumberException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/property', name: 'property_')]
class PropertyController extends AbstractController
{

    public function __construct(
        private TranslatorInterface $translator,
        private EntityManagerInterface $manager
    ){}

    #[Route('/', name: 'index')]
    public function index(Request $request, PropertyRepository $repository, PaginatorInterface $paginator): Response
    {
        $data = $repository->findAll();

        $properties = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6
        );
        
        return $this->render('property/index.html.twig', [
            'properties' => $properties
        ]);
        
    }

    #[Route('/{id}', name: 'view')]
    public function view(
        Property $property,
        InvestmentService $service, 
        Request $request, 
        InvestmentRepository $repository,
        HashidsInterface $hasher,
        WorkflowInterface $investmentStatusStateMachine,
        WorkflowInterface $propertyStatusStateMachine
    ){


        $investment = (new Investment())
                        ->setUser($this->getUser())
                        ->setProperty($property);

        $form = $this->createForm(BuyPartType::class, $investment);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('_token');
        if($form->isSubmitted() && $form->isValid()){
            

            try
            {    

                $service->checkInvestment($investment);

                // Enregistre l'investissement si c'est nouveau ou met à jours si l'utilisateur en a déjà un
                $service->saveInvestment($investment);

                try{

                    $investmentStatusStateMachine->apply($investment, 'buy');

                }catch(NotEnabledTransitionException $e){

                    $blockers = $e->getTransitionBlockerList();

                    foreach($blockers as $blocker){
                        $this->addFlash('error', $blocker->getMessage());
                    }

                    return $this->redirectToRoute('property_view', ["id" => $hasher->encode($property->getId())]);
                }

                // On verifie si la propriété a été totalement investi
                $availableParts = $service->getAvailableParts($property);
            
                if($availableParts == 0){
                    $propertyStatusStateMachine->apply($property, 'to_funded');
                    $this->manager->persist($property);
                }

                $this->manager->flush();
                $this->addFlash('success', $this->translator->trans('Achat effectué avec success'));

            }
            catch(InsuffientBalanceException $e){
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }catch(ToMuchPartsNumberException $e){
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }

            return $this->redirectToRoute('property_view', ["id" => $hasher->encode($property->getId())]);

        }

        $availableParts = $service->getAvailableParts($property);
        
        return $this->render('property/details.html.twig', [
            'property' => $property,
            'investment' => $investment,
            'form' => $form->createView(),
            'availableParts' => $availableParts
        ]);
        
    }

}
