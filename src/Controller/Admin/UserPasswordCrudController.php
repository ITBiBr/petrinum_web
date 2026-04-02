<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Length;

class UserPasswordCrudController extends UserCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', 'New password')
            ->setSearchFields(null);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);
        $actions->disable(Action::INDEX);
        $actions->disable(Action::DETAIL);
        $actions->disable(Action::SAVE_AND_RETURN);
        return $actions;
    }

    public function edit(AdminContext $context): KeyValueStore|Response
    {
        $user = $this->getUser();
        $entity = $context->getEntity()->getInstance();

        // Zkontroluj ID entity
        if ($entity->getUsername() !== $user->getUserIdentifier()) {
            throw $this->createAccessDeniedException();
        }

        return parent::edit($context);
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('password', )
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat password'],
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Password must be at least 8 characters long.',
                    ]),],
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
        ;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $this->addFlash('success', 'Saved.');

        parent::updateEntity($entityManager, $entityInstance);
    }

}
