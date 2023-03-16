<?php

namespace App\Controller\Admin;

use App\Entity\Security\User;
use App\Entity\Property\Property;
use App\Entity\Wallet\WithdrawalRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerateur)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
       $url = $this->adminUrlGenerateur
                ->setController(PropertyCrudController::class)
                ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blok Mu');
    }

    public function configureMenuItems(): iterable
    {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            yield MenuItem::linkToCrud('Property', 'fas fa-list', Property::class);
            yield MenuItem::linkToCrud('Withdrawal', 'fa-solid fa-money-bill-transfer', WithdrawalRequest::class);
            yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);
    }
}
