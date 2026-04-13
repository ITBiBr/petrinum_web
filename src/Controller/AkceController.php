<?php

namespace App\Controller;

use App\Controller\Admin\UrlTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AkceController extends AbstractController
{
    use UrlTrait;
    #[Route('/akce', name: 'app_akce')]
    public function index(): Response
    {
        return $this->render('akce/index.html.twig', [

        ]);
    }
}
