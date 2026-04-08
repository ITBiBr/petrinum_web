<?php

namespace App\Controller;

use App\Repository\AktualityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DevController
{
    #[Route('/dev/generate-slugs', name: 'dev_generate_slugs')]
    public function generateSlugs(
        AktualityRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $slugger = new AsciiSlugger();

        $items = $repo->findAll();

        foreach ($items as $item) {
            if ($item->getUrl()) {
                continue; // přeskočí už vyplněné (volitelné)
            }

            $slug = $slugger->slug($item->getTitulek())->lower();

            $item->setUrl($slug);
        }

        $em->flush();

        return new Response('Hotovo 👍');
    }
}
