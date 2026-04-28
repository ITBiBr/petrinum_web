<?php

namespace App\Controller;

use App\Repository\ZamestnanciKategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ZamestnanciController extends AbstractController
{
    #[Route('/zamestnanci', name: 'app_zamestnanci')]
    public function index(ZamestnanciKategorieRepository $zamestnanciKategorieRepository): Response
    {

        return $this->render('zamestnanci/index.html.twig', [
            'kategorieZamestnanci' => $zamestnanciKategorieRepository->findBy([],['poradi' => 'ASC']),
        ]);
    }
}
