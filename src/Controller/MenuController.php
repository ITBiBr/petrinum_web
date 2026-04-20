<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MenuController extends AbstractController
{
    public function __construct(
        private MenuRepository $menuRepository
    ) {}

    public function menu(): Response
    {
        $menu = $this->menuRepository->findTree();

        return $this->render('menu/menu.html.twig', [
            'menu' => $menu,
        ]);
    }
}
