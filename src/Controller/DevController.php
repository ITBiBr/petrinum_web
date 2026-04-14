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
            $existingUrls[] = $url;
        }

        $em->flush();

        return new Response('Hotovo');
    }

    #[Route('/dev/generate-perex', name: 'dev_generate_perex')]
    public function generatePerex(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Akce::class);
        $items = $repo->findAll();

        foreach ($items as $item) {
            $obsah = $item->getObsah();
            if ($obsah == '')
                $obsah = $item->getObsahPokracovani();

            if ($obsah) {
                $text = trim(strip_tags($obsah));
                $limit = 160;

                if (mb_strlen($text) > $limit) {
                    // vezmeme o trochu víc, ať máme prostor najít celé slovo
                    $cut = mb_substr($text, 0, $limit + 10);

                    // najdeme poslední mezeru
                    $lastSpace = mb_strrpos($cut, ' ');

                    if ($lastSpace !== false) {
                        $perex = mb_substr($cut, 0, $lastSpace) . '...';
                    } else {
                        // fallback když není mezera
                        $perex = mb_substr($text, 0, $limit) . '...';
                    }
                } else {
                    $perex = $text;
                }

                $item->setPerex($perex);
            }
        }

        $em->flush();

        return new Response('Hotovo!');
    }


    #[Route('/dev/replace-tags', name: 'dev_replace_tags')]
    public function replaceITags(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Akce::class);
        $items = $repo->findAll();

        foreach ($items as $item) {
            $obsah = $item->getObsah();
            $obsahPokracovani = $item->getObsahPokracovani();

            if ($obsah) {
                $obsah = preg_replace('/\[i\](.*?)\[\/i\]/is', '<i>$1</i>', $obsah);
                $obsah = preg_replace('/\[b\](.*?)\[\/b\]/is', '<strong>$1</strong>', $obsah);
                $obsah = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $obsah);
                $item->setObsah($obsah);
            }

            if ($obsahPokracovani) {
                $obsahPokracovani = preg_replace('/\[i\](.*?)\[\/i\]/is', '<i>$1</i>', $obsahPokracovani);
                $obsahPokracovani = preg_replace('/\[b\](.*?)\[\/b\]/is', '<strong>$1</strong>', $obsahPokracovani);
                $obsahPokracovani = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $obsahPokracovani);
                $item->setObsahPokracovani($obsahPokracovani);
            }
        }

        $em->flush();

        return new Response('Hotovo!');
    }


}
