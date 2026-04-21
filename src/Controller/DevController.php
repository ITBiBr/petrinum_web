<?php

namespace App\Controller;

use App\Controller\Admin\EditTextTrait;
use App\Entity\Akce;
use App\Repository\AkceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DevController
{
    use EditTextTrait;
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
                $obsah = str_replace('\r\n', ' <br>', $obsah);

                $item->setObsah($obsah);
            }

            if ($obsahPokracovani) {
                $obsahPokracovani = preg_replace('/\[i\](.*?)\[\/i\]/is', '<i>$1</i>', $obsahPokracovani);
                $obsahPokracovani = preg_replace('/\[b\](.*?)\[\/b\]/is', '<strong>$1</strong>', $obsahPokracovani);
                $obsahPokracovani = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $obsahPokracovani);
                $obsahPokracovani = str_replace('\r\n', ' <br>', $obsahPokracovani);

                $item->setObsahPokracovani($obsahPokracovani);
            }
        }

        $em->flush();

        return new Response('Hotovo!');
    }

    #[Route('/dev/add-nbsp', name: 'dev_add_nbsp')]
    public function nbsp(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Akce::class);
        $items = $repo->findAll();

        foreach ($items as $item) {
            $item->setObsah($this->addNbsp($item->getObsah()));
            $item->setObsahPokracovani($this->addNbsp($item->getObsahPokracovani()));
            $item->setPerex($this->addNbsp($item->getPerex()));
        }

        $em->flush();

        return new Response('Hotovo!');
    }


    #[Route('/dev/fill-date-from-text', name: 'dev_fill_date_from_text')]
    public function fillDateFromText(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Akce::class);
        $items = $repo->findAll();

        foreach ($items as $item) {
            $datum = $item->getDatum();

            if ($datum !== null && $datum->format('Y') > 1900) {
                continue;
            }

            $text = $item->getObsah() ?: '';
            $text .= ' ' . ($item->getObsahPokracovani() ?: '');

            $day = $month = $year = null;

            // 1. dd.mm.yyyy
            if (preg_match('/(\d{1,2})\.\s*(\d{1,2})\.\s*(\d{4})/', $text, $m)) {
                $day = $m[1];
                $month = $m[2];
                $year = $m[3];
            }
            // 2. 1. února 2011
            elseif (preg_match('/(\d{1,2})\.\s*([a-záčďéěíňóřšťúůýž]+)\s*(\d{4})/iu', $text, $m)) {

                $day = $m[1];
                $monthName = mb_strtolower($m[2]);
                $year = $m[3];

                $months = [
                    'ledna' => 1,
                    'února' => 2,
                    'brezna' => 3,
                    'března' => 3,
                    'dubna' => 4,
                    'května' => 5,
                    'kvetna' => 5,
                    'června' => 6,
                    'cervna' => 6,
                    'července' => 7,
                    'cervence' => 7,
                    'srpna' => 8,
                    'září' => 9,
                    'zari' => 9,
                    'října' => 10,
                    'rijna' => 10,
                    'listopadu' => 11,
                    'prosince' => 12,
                ];

                if (!isset($months[$monthName])) {
                    continue;
                }

                $month = $months[$monthName];
            } else {
                continue;
            }

            if ($day && $month && $year && checkdate($month, $day, $year)) {
                $date = new \DateTime(sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $day));
                $item->setDatum($date);
            }
        }

        $em->flush();


        return new Response('Hotovo!');
    }



}
