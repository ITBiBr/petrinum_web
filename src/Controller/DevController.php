<?php

namespace App\Controller;

use App\Entity\Akce;
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

    #[Route('/dev/generate-perex', name: 'dev_generate_perex')]
    public function generatePerex(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Akce::class);
        $items = $repo->findAll();

        foreach ($items as $item) {
            $obsah = $item->getObsah();

            if ($obsah) {
                // odstranění HTML tagů (doporučeno)
                $text = strip_tags($obsah);

                // zkrácení na 50 znaků (UTF-8 safe)
                if (mb_strlen($text) > 50) {
                    $perex = mb_substr($text, 0, 50) . '...';
                } else {
                    $perex = $text;
                }

                $item->setPerex($perex);
            }
        }

        $em->flush();

        return new Response('Hotovo!');
    }
}
