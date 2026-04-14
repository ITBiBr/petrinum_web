<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();
        return $this->render('admin/index.html.twig');
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Petrinum');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Web', 'fa fa-globe', $this->generateUrl('app_main'));
        yield MenuItem::linkToDashboard('Home', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-user')->setPermission('ROLE_ADMIN');


        // odkaz na změnu hesla aktuálního uživatele
        $currentUserId = $this->getUser()?->getId();

        if ($currentUserId) {
            $url = $this->container->get(AdminUrlGenerator::class)
                ->setController(UserPasswordCrudController::class)
                ->setAction('edit')
                ->setEntityId($currentUserId)
                ->generateUrl();

            yield MenuItem::linkToUrl('Password change', 'fa fa-key', $url)->setPermission('ROLE_EDITOR');
        }
        yield MenuItem::linkTo(ClankyCrudController::class, 'Articles', 'fa fa-newspaper')->setPermission('ROLE_EDITOR');
        yield MenuItem::linkTo(AkceCrudController::class, 'Events', 'fa fa-calendar')->setPermission('ROLE_EDITOR');
        yield MenuItem::linkTo(StitkyCrudController::class, 'Labels', 'fa fa-tags', )->setPermission('ROLE_EDITOR');
    }
}
