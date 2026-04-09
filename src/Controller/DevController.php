<?php

namespace App\Controller;

use App\Repository\AkceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DevController
{
    #[Route('/dev/generate-slugs', name: 'dev_generate_slugs')]
    public function generateSlugs(
        AkceRepository         $repo,
        EntityManagerInterface $em
    ): Response {
        $slugger = new AsciiSlugger();

        $items = $repo->findAll();

        // vezmeme existující URL z DB
        $existingUrls = [];
        foreach ($items as $item) {
            if ($item->getUrl()) {
                $existingUrls[] = $item->getUrl();
            }
        }

        foreach ($items as $item) {
            if ($item->getUrl()) {
                continue;
            }

            $originalUrl = (string) $slugger->slug($item->getTitulek())->lower();
            $url = $originalUrl;

            $i = 2;

            while (in_array($url, $existingUrls, true)) {
                $url = $originalUrl . '-' . $i;
                $i++;
            }

            $item->setUrl($url);
            $existingUrls[] = $url; // 🔥 klíčové
        }

        $em->flush();

        return new Response('Hotovo 👍');
    }
}
