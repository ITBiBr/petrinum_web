<?php

namespace App\Controller;

use App\Repository\ClankyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ClankyRepository $clankyRepository): Response
    {
        $clanek = $clankyRepository->findOneBy(['url' => 'uvod']);
        if (!$clanek)
            return new Response('Článek "úvod" byl pravděpodobně odstraněn.');
        return $this->render('clanky/clanek.html.twig', [
            'controller_name' => 'AkceController',
            'akce' => $clanek,
        ]);
    }
}
