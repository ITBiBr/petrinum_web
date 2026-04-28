<?php

namespace App\Controller\Admin;

use App\Entity\ZamestnanciKategorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ZamestnanciKategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ZamestnanciKategorie::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('admin/form/dropzone_theme.html.twig')
            ->setPageTitle('new', 'New employee category')
            ->setPageTitle('edit', 'Edit employee category')
            ->setPageTitle('index', 'Employee categories')
            ->setDefaultSort(['poradi' => 'ASC']);
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('New employee category');
            });
    }
    public function __construct(private readonly Security $security)
    {
    }
    public function configureFields(string $pageName): iterable
    {
        if (!$this->security->isGranted('ROLE_EDITOR'))
            throw new AccessDeniedException('Access Denied');
        yield IntegerField::new('poradi', 'Order');
        yield TextField::new('nazev', 'Title');

    }
}
