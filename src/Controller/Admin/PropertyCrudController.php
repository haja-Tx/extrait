<?php

namespace App\Controller\Admin;

use Hashids\Hashids;
use App\Form\FeatureType;
use App\Form\PictureType;
use App\Entity\Property\Property;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Workflow\WorkflowInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class PropertyCrudController extends AbstractCrudController
{

    public function __construct(
        private WorkflowInterface $propertyStatusStateMachine
    ){}

    public static function getEntityFqcn(): string
    {
        return Property::class;
    }


    public function configureActions(Actions $actions): Actions
    {

        $openPurchase = Action::new('open_purchase', 'Open blok purchase', 'fas fa-lock-open')
            ->linkToCrudAction('openPurchase')
            ->displayIf(function ($property) {
                return $property->getStatus() == 'published';
            })
            ->setCssClass('btn btn-primary')
        ;

        $activate = Action::new('begin_revenue', 'Start revenue return', 'fa fa-play')
            ->linkToCrudAction('activateProperty')
            ->displayIf(function ($property) {
                return $this->propertyStatusStateMachine->can($property, 'to_active');
            })
            ->setCssClass('btn btn-primary')
        ;

        $markAsSold = Action::new ('mark_as_sold', 'Mark as sold', 'fas fa-money')
            ->linkToCrudAction('markAsSold')
            ->displayIf(function ($property) {
                return $this->propertyStatusStateMachine->can($property, 'sell') && !is_null($property->getPartSellingPrice());
            })
            ->setCssClass('btn btn-primary')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $openPurchase)
            ->add(Crud::PAGE_EDIT, $openPurchase)
            ->add(Crud::PAGE_DETAIL, $activate)
            ->add(Crud::PAGE_EDIT, $activate)
            ->add(Crud::PAGE_DETAIL, $markAsSold)
        ;
    }


   
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex()
            ,

            // Onglet détail 
            FormField::addTab('Detail'),

            TextField::new('title', 'Name'),
            ImageField::new('thumbnail', 'Thumbnail')
                ->setBasePath($this->getParameter("app.path.thumbnail_images"))
                ->hideOnForm()
            ,

            TextField::new('imageFile', 'Thumbnail')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
                
            TextEditorField::new('description', 'Description')
                ->setTemplatePath('admin/crud/fields/text_editor.html.twig')
                ->hideOnIndex()
            ,

            TextField::new('status', 'Status')
                ->onlyOnIndex()
            ,

            // Onglet Informations
            FormField::addTab('Financial informations')
            ,
            MoneyField::new('estimate_price', 'Price estimate')
                ->setCurrency('EUR')
                ->setCustomOption(MoneyField::OPTION_STORED_AS_CENTS, false)
                ->setRequired(true)
            ,
            MoneyField::new('part_price', "Blok price")
                ->setCurrency('EUR')
                ->setCustomOption(MoneyField::OPTION_STORED_AS_CENTS, false)
                ->setRequired(true)
                ,
            PercentField::new('rate_of_return', 'Rate of return')
                ->setCustomOption(PercentField::OPTION_NUM_DECIMALS, 2)
                ->setCustomOption(PercentField::OPTION_STORED_AS_FRACTIONAL, false)
                ->setRequired(true),

            // Onglet Images
            FormField::addTab('Illustrations')
            ,
            CollectionField::new('pictures', 'Illustrations')
                ->hideOnIndex()
                ->setFormTypeOption('allow_add', true)
                ->setFormTypeOption('allow_delete', true)
                ->setFormTypeOption('entry_type', PictureType::class)
            ,

            
            // Onglet Caracteristique
            FormField::addTab('Features')
            ,
            CollectionField::new('features', 'Features')
                ->hideOnDetail()
                ->hideOnIndex()
                ->setFormTypeOption('allow_add', true)
                ->setFormTypeOption('allow_delete', true)
                ->setFormTypeOption('entry_type', FeatureType::class)
            ,

            FormField::addTab('Selling information'),
            MoneyField::new('partSellingPrice', 'Selling price of blok')
                ->setCurrency('EUR')
                ->setCustomOption(MoneyField::OPTION_STORED_AS_CENTS, false)
                ->setRequired(false)
        ];
    }


    public function openPurchase(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $manager){

        $property = $context->getEntity()->getInstance();

        $this->propertyStatusStateMachine->apply($property, 'open_purchase');

        $manager->persist($property);
        $manager->flush();

        $this->addFlash('success', 'The purchase of bloks is now open');

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);

    }

    public function activateProperty(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $manager){
        $property = $context->getEntity()->getInstance();

        $this->propertyStatusStateMachine->apply($property, 'to_active');

        $manager->persist($property);
        $manager->flush();

        $this->addFlash('success', 'Investments will start to pay off');

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function markAsSold(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $manager){
        $property = $context->getEntity()->getInstance();

        $this->propertyStatusStateMachine->apply($property, 'sell');

        $manager->persist($property);
        $manager->flush();

        $this->addFlash('success', 'Property mark as sold successfully');

        $url = $adminUrlGenerator
            ->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

}
