<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Security\Files;
use App\Form\UserIdentityType;
use App\Form\ChangePasswordType;
use App\Form\Model\ChangePassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use TheSeer\Tokenizer\Exception;

#[Route('/account', name: 'account_')]
class AccountController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $user = $this-> getUser();
        $form = $this->createForm(UserType::class, $this->getUser());
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $this->manager->flush();
            
            $this->addFlash('success',"Votre compte a bien été modifié");
            
            return $this->redirectToRoute('account_index');
            
        }

        return $this->render('account/profile-settings.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/change-password', name: 'password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $passwordForm = $this->createForm(changePasswordType::class, new ChangePassword());
        $passwordForm->handleRequest($request);

        if($passwordForm->isSubmitted() && $passwordForm->isValid()){
  
            $user = $this->getUser();
            $user->setPassword(
                $encoder->hashPassword(
                    $user,
                    $passwordForm->getData()->plainPassword
                )
            );

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success',"Votre mot de passe a bien été modifié");

            return $this->redirect($this->generateUrl('account_password')); 

        }


        return $this->render('account/change-password.html.twig', [
            'passwordForm' => $passwordForm->createView()
        ]);

    }

    #[Route('/identity-files', name: 'files')]
    public function uploadFiles(Request $request, WorkflowInterface $userStatusStateMachine){

        $user = $this->getUser();

        if(!is_null($user->getFiles())){
           $userFiles =  $user->getFiles();
        }else{
            $userFiles = (new Files())
                ->setUser($user);
            ;
        }

        $identityForm = $this->createForm(UserIdentityType::class, $userFiles);

        $identityForm->handleRequest($request);
        if($identityForm->isSubmitted()){
     
            $this->addFlash('success', 'Files uploaded successfully');

            try{
                $userStatusStateMachine->apply($this->getUser(), 'identity_pending');
            }catch(\Exception $e){
                $this->addFlash('error', 'An error occurred during the operation');
                return $this->redirectToRoute('account_files');
            }

            $this->manager->persist($userFiles);
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('account_files');

        }
        return $this->render('account/user-documents.html.twig',[
            'identityForm' =>$identityForm->createView()
        ]);
    }
}
