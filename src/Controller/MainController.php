<?php

namespace App\Controller;

use App\Entity\Clanky;
use App\Repository\ClankyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ClankyRepository $clankyRepository): Response
    {
        $clanek = $clankyRepository->findOneBy(['url' => 'uvod']);
        if (!$clanek) {
                    $clanek = new Clanky();
                    $clanek->setObsah('Článek "Úvod" nebyl nalezen!');
                }
        return $this->render('clanky/clanek.html.twig', [
            'controller_name' => 'AkceController',
            'clanek' => $clanek,
        ]);
    }
}
