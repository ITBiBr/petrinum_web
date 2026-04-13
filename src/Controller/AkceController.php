<?php

namespace App\Controller;

use App\Controller\Admin\UrlTrait;
use App\Repository\AkceRepository;
use App\Repository\StitkyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AkceController extends AbstractController
{
    use UrlTrait;
    #[Route('/akce/{stitek?}', name: 'app_akce')]
    #[Route('/archiv-akci/{stitek?}', name: 'app_archiv_akci')]
    public function index(StitkyRepository $stitkyRepository, AkceRepository $akceRepository, ?string $stitek = null, Request $request): Response {
        $limit = 12;
        $offset = 0;
        $jeProbehle = $request->attributes
                ->get('_route') === 'app_archiv_akci';
        $aktivniStitek = $stitek ? $stitkyRepository->findOneBy(['url' => $stitek]) : null;

        $stitky = $stitkyRepository->findStitkySAkcemi();
        $akce = $akceRepository->findAkceKZobrazeniPaginated($limit + 1, $offset, $aktivniStitek, $jeProbehle);

        $hasMore = count($akce) > $limit;
        $akce = array_slice($akce, 0, $limit);

        return $this->render('akce/index.html.twig', [
            'limit' => $limit,
            'offset' => $offset,
            'akce' => $akce,
            'stitky' => $stitky,
            'hasMore' => $hasMore,
            'aktivniStitek' => $aktivniStitek,
            'probehle' => $jeProbehle
        ]);
    }

    #[Route('/akce-load-more/{stitek?}', name: 'app_akce_load_more')]
    #[Route('/archiv-akci-load-more/{stitek?}', name: 'app_archiv_akci_load_more')]
    public function loadMore(Request $request, StitkyRepository $stitkyRepository, AkceRepository $akceRepository, ?string $stitek = null): Response {
        $limit = 9;
        $offset = (int) $request->query->get('offset', 0);
        $jeProbehle = $request->attributes
                ->get('_route') === 'app_archiv_akci_load_more';

        $aktivniStitek = $stitek ? $stitkyRepository->findOneBy(['url' => $stitek]) : null;

        // načteme o 1 víc
        $data = $akceRepository->findAkceKZobrazeniPaginated($limit + 1, $offset, $aktivniStitek, $jeProbehle);

        $hasMore = count($data) > $limit;
        $akce = array_slice($data, 0, $limit);

        // vyrenderujeme HTML fragment
        $html = $this->renderView('akce/_akce_load_more.html.twig', [
            'akce' => $akce,
        ]);

        return $this->json([
            'html' => $html,
            'hasMore' => $hasMore,
            'newOffset' => $offset + $limit,
            'probehle' => $jeProbehle
        ]);
    }
}
