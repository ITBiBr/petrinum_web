<?php

namespace App\Controller\Admin;

use App\Entity\Clanky;
use App\Form\Type\DropzoneType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class ClankyCrudController extends AbstractCrudController
{
    use UrlTrait {
        persistEntity as persistEntityFromTrait;
    }

    use EditTextTrait;
    public static function getEntityFqcn(): string
    {
        return Clanky::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('admin/form/dropzone_theme.html.twig')
            ->setPageTitle('new', 'New Article')
            ->setPageTitle('edit', 'Edit Article')
            ->setPageTitle('index', 'Articles');
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
                return $action->setLabel('New Article');
            })
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE)
        ;
    }
    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addWebpackEncoreEntry('admin');
    }

    public function __construct(private readonly Security $security)
    {
    }

    public function configureFields(string $pageName): iterable
    {
        if (!$this->security->isGranted('ROLE_EDITOR'))
            throw new AccessDeniedException('Access Denied');

        yield IdField::new('id')->hideOnForm();
        yield TextField::new('titulek', 'Title');
        yield BooleanField::new('isZobrazitTitulek', 'Show Title');
        yield TextField::new('url', 'URL')->hideOnForm();
        yield TextEditorField::new('obsah');
        yield TextField::new('Video', 'Video (YouTube ID)')->hideOnIndex();

        yield TextEditorField::new('obsahPokracovani' ,'Article content - continued')->hideOnIndex();
        yield Field::new('upload')
            ->setFormType(DropzoneType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'data-entity' => 'clanky',
                    'data-entity-id' => $this->getContext()?->getEntity()?->getInstance()?->getId(),
                ],
            ])
            ->onlyOnForms()
            ->setHelp('Content of this field saves automatically.');

    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->persistEntityFromTrait($entityManager, $this->nbsp($entityInstance));
    }


    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        parent::updateEntity($entityManager,$this->nbsp($entityInstance));
    }

    private function nbsp($entityInstance){
        if ($entityInstance instanceof Clanky) {
            $entityInstance->setObsah($this->addNbsp($entityInstance->getObsah()));
            $entityInstance->setObsahPokracovani($this->addNbsp($entityInstance->getObsahPokracovani()));
        }
        return $entityInstance;
    }

}
