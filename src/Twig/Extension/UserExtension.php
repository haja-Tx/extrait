<?php

namespace App\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Security\User;
use Twig\Extension\AbstractExtension;
use App\Service\Financial\WalletService;

class UserExtension extends AbstractExtension
{


    public function __construct(
        private WalletService $service
    ){}

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            // new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getUserBalance', [$this, 'getUserBalance']),
        ];
    }

    public function getUserBalance(User $user)
    {
        return $this->service->computeBalance($user);
    }
}
