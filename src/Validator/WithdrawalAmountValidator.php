<?php

namespace App\Validator;

use App\Service\Financial\WalletService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintValidator;

class WithdrawalAmountValidator extends ConstraintValidator
{

    public function __construct(
        private Security $security,
        private WalletService $walletService
    ){}

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\WithdrawalAmount $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $balance = $this->walletService->computeBalance($this->security->getUser());

        if($balance >= $value){
            return;
        }


        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
