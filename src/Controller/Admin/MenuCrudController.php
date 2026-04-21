<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'New Menu Item')
            ->setPageTitle('edit', 'Edit Menu Item')
            ->setPageTitle('index', 'Menu Items');
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
            return $action->setLabel('New Menu Item');
        });
        return $actions;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nazev', 'Title'),

            TextField::new('routeName', 'Route Name')
                ->setHelp('E.g. app_homepage'),

            AssociationField::new('Clanky','Articles')
                ->setHelp('Replaces Route Name'),

            AssociationField::new('parent', 'Parent Menu'),

            IntegerField::new('position', 'Position'),
        ];
    }
}
