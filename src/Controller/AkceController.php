<?php

namespace App\Controller;

use App\Controller\Admin\UrlTrait;
use App\Entity\Akce;
use App\Repository\AkceRepository;
use App\Repository\StitkyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AkceController extends AbstractController
{
    use UrlTrait;
    #[Route('/kalendar-akci/{stitek?}', name: 'app_akce')]
    #[Route('/archiv-akci/{stitek?}', name: 'app_archiv_akci')]
    #[Route('/archiv-akci-mesicni/{rok}/{mesic}/{stitek?}', name: 'app_archiv_akci_mesic')]
    public function index(Request $request, StitkyRepository $stitkyRepository, AkceRepository $akceRepository, ?string $stitek = null, ?int $rok = null, ?int $mesic = null): Response {
        $limit = 12;
        $offset = 0;
        $jeMesicni = $request->attributes
                ->get('_route') === 'app_archiv_akci_mesic';
        $jeProbehle = ($request->attributes
                ->get('_route') === 'app_archiv_akci' or $jeMesicni);

        $aktivniStitek = $stitek ? $stitkyRepository->findOneBy(['url' => $stitek]) : null;

        $stitky = $stitkyRepository->findStitkySPlatnymiAkcemi($jeProbehle, $mesic, $rok);
        $akce = $akceRepository->findAkceKZobrazeniPaginated($limit + 1, $offset, $aktivniStitek, $jeProbehle, $mesic, $rok);

        $archive = $akceRepository->getArchiveStructured();
        $hasMore = count($akce) > $limit;
        $akce = array_slice($akce, 0, $limit);

        return $this->render('akce/index.html.twig', [
            'limit' => $limit,
            'offset' => $offset,
            'akce' => $akce,
            'stitky' => $stitky,
            'hasMore' => $hasMore,
            'aktivniStitek' => $aktivniStitek,
            'probehle' => $jeProbehle,
            'mesicni' => $jeMesicni,
            'mesic' => $mesic,
            'rok' => $rok,
            'archive' => $archive,
        ]);
    }

    #[Route('/akce-load-more/{stitek?}', name: 'app_akce_load_more')]
    #[Route('/archiv-akci-load-more/{stitek?}', name: 'app_archiv_akci_load_more')]
    #[Route('/archiv-akci-load-more-mesicni/{rok}/{mesic}/{stitek?}', name: 'app_archiv_akci_load_more_mesic')]
    public function loadMore(Request $request, StitkyRepository $stitkyRepository, AkceRepository $akceRepository, ?string $stitek = null, ?int $rok = null, ?int $mesic = null): Response {
        $limit = 9;
        $offset = (int) $request->query->get('offset', 0);
        $jeProbehle = ($request->attributes
                ->get('_route') === 'app_archiv_akci_load_more 'or $request->attributes
                ->get('_route') === 'app_archiv_akci_load_more_mesic');

        $aktivniStitek = $stitek ? $stitkyRepository->findOneBy(['url' => $stitek]) : null;

        // načteme o 1 víc
        $data = $akceRepository->findAkceKZobrazeniPaginated($limit + 1, $offset, $aktivniStitek, $jeProbehle, $mesic, $rok);

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

    #[Route('/akce/{url}', name: 'akce_url')]
    public function showAkce(string $url, EntityManagerInterface $entityManager): Response
    {
        $akce = $entityManager->getRepository(Akce::class)->findOneBy(['url'=>$url]);

        if (!$akce)
            throw new NotFoundHttpException();
        return $this->render('akce/akce.html.twig', [
            'controller_name' => 'AkceController',
            'akce' => $akce,
            'paticka'=> true,
        ]);
    }
}
