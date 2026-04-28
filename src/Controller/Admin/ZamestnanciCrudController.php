<?php

namespace App\Controller\Admin;

use App\Entity\Zamestnanci;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\File;

class ZamestnanciCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Zamestnanci::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('admin/form/dropzone_theme.html.twig')
            ->setPageTitle('new', 'New employee')
            ->setPageTitle('edit', 'Edit employee')
            ->setPageTitle('index', 'Employees')
            ->setDefaultSort(['ZamestnanciKategorie.poradi' => 'ASC', 'poradi' => 'ASC']);
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('New employee');
            });
    }
    public function __construct(private readonly Security $security)
    {
    }
    public function configureFields(string $pageName): iterable
    {
        if (!$this->security->isGranted('ROLE_EDITOR'))
            throw new AccessDeniedException('Access Denied');
        yield TextField::new('jmeno', 'Name');
        yield TextField::new('role', 'Role');
        yield AssociationField::new('ZamestnanciKategorie', 'Category');
        yield ImageField::new('Foto', 'Photo')
            ->setBasePath('images/zamestnanci')
            ->setUploadDir('public/images/zamestnanci')
            ->setFormTypeOption('multiple', false)
            ->setUploadedFileNamePattern('[timestamp]-[slug].[extension]')

            ->setSortable(false);
        yield IntegerField::new('poradi', 'Order');
    }

}
