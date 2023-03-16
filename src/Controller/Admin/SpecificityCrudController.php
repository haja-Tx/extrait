<?php

namespace App\Controller\Admin;

use App\Entity\Property\Specificity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SpecificityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Specificity::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
