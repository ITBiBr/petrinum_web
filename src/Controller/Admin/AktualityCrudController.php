<?php

namespace App\Controller\Admin;

use App\Entity\Aktuality;
use App\Form\Type\DropzoneType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AktualityCrudController extends AbstractCrudController
{
    use UrlTrait;
    public static function getEntityFqcn(): string
    {
        return Aktuality::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin');
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE)
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('admin/form/dropzone_theme.html.twig');
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('titulek', 'Title'),
            Field::new('upload')
                ->setFormType(DropzoneType::class)
                ->setFormTypeOptions([
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'data-entity' => 'aktuality',
                        'data-entity-id' => $this->getContext()?->getEntity()?->getInstance()?->getId(),
                    ],
                ])
                ->onlyOnForms()

        ];
    }



}
