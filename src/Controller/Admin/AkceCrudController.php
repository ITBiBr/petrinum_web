<?php

namespace App\Controller\Admin;

use App\Entity\Akce;
use App\Form\Type\DropzoneType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class AkceCrudController extends AbstractCrudController
{
    use UrlTrait {
        persistEntity as persistEntityFromTrait;
    }
    use EditTextTrait;
    public static function getEntityFqcn(): string
    {
        return Akce::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->addFormTheme('admin/form/dropzone_theme.html.twig')
            ->setPageTitle('new', 'New event')
            ->setPageTitle('edit', 'Edit event')
            ->setPageTitle('index', 'Events');
    }
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function(Action $action){
                return $action->setLabel('New event');
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
        yield DateField::new('datum', 'Date');
        yield TextField::new('titulek', 'Title');
        yield TextEditorField::new('obsah', 'Content');
        yield TextEditorField::new('obsahPokracovani' ,'Article content - continued')->hideOnIndex();
        yield AssociationField::new('stitkies', 'Labels')->setFormTypeOption('by_reference', false)->formatValue(fn($value) => implode('<br>', $value->toArray()));

        yield Field::new('upload')
            ->setFormType(DropzoneType::class)
            ->setFormTypeOptions([
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'data-entity' => 'akce',
                    'data-entity-id' => $this->getContext()?->getEntity()?->getInstance()?->getId(),
                ],
            ])
            ->onlyOnForms()
            ->setHelp('Content of this field saves automatically.');
        yield TextField::new('Video', 'Video (YouTube ID)')->hideOnIndex();

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
        if ($entityInstance instanceof Akce) {
            $entityInstance->setPerex($this->addNbsp($entityInstance->getPerex()));
            $entityInstance->setObsah($this->addNbsp($entityInstance->getObsah()));
            $entityInstance->setObsahPokracovani($this->addNbsp($entityInstance->getObsahPokracovani()));
        }
        return $entityInstance;
    }

}
