<?php

namespace App\Controller;

use App\Repository\AkceRepository;
use App\Repository\ClankyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

final class HledaniController extends AbstractController
{
    #[Route('/hledani', name: 'app_hledani', methods: ['GET'])]
    public function index(
        Request $request,
        AkceRepository $akceRepository,
        ClankyRepository $clankyRepository,
        RateLimiterFactory $searchLimiter
    ): JsonResponse {
        $limiter = $searchLimiter->create($request->getClientIp());

        if (!$limiter->consume()->isAccepted()) {
            return $this->json([
                'error' => 'Příliš mnoho požadavků'
            ], 429);
        }

        $term = trim($request->query->get('q', ''));

        if (
            mb_strlen($term) < 2 || //delší než dva znaky a kratší než 100 znaků
            mb_strlen($term) > 100
        ) {
            return $this->json([]);
        }

        $results = [];

        foreach ($akceRepository->search($term) as $akce) {
            $results[] = [
                'type' => 'akce',
                'title' => $akce->getTitulek(),
                'url' => '/akce/' . $akce->getUrl(),
            ];
        }

        foreach ($clankyRepository->search($term) as $clanek) {
            $results[] = [
                'type' => 'clanek',
                'title' => $clanek->getTitulek(),
                'url' => '/clanky/' . $clanek->getUrl(),
            ];
        }

        return $this->json($results);
    }
}
