<?php

namespace App\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class UtilsExtension extends AbstractExtension
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){}

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('adminUrl', [$this, 'getAdminUrl']),
        ];
    }

    public function getAdminUrl(?string $controller=null, ?string $action=null, ?int $id=null): string
    {
        $url = $this->adminUrlGenerator;
        if($controller){
            $url->setController($controller);
        }
        if($action){
            $url->setAction($action);
        }
        if($id){
            $url->setEntityId($id);
        }
        return $url->generateUrl();
    }
}
