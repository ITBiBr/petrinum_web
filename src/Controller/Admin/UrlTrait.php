<?php

namespace App\Controller\Admin;

use App\Entity\Clanky;
use Doctrine\ORM\EntityManagerInterface;

trait UrlTrait
{
    private function makeURL(string $url): string
    {
        // 1. Odstranění diakritiky
        $url = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $url);

        // 2. Nahrazení mezer pomlckami
        $url = preg_replace('/\s+/', '-', $url);

        // 3. Odstranění nepovolených znaků (ponechá jen písmena, čísla, pomlčku, podtržítko)
        $url = preg_replace('/[^A-Za-z0-9\-_]/', '', $url);

        // 4. Volitelně: převede na lowercase
        $url = strtolower($url);

        return $url;
    }

    protected function makeUniqueUrl(string $original, EntityManagerInterface $em, string $entityClass): string
    {
        $url = $originalUrl = $this->makeURL($original);

        $i = 2;

        while ($em->getRepository($entityClass)->findOneBy(['url' => $url])) {
            $url = $originalUrl . '-' . $i;
            $i++;
        }

        return $url;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (method_exists($entityInstance, 'getTitulek') &&
            method_exists($entityInstance, 'setUrl')) {

            $entityClass = get_class($entityInstance);

            $entityInstance->setUrl(
                $this->makeUniqueUrl(
                    $entityInstance->getTitulek(),
                    $entityManager,
                    $entityClass
                )
            );
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}