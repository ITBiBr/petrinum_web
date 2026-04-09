<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

trait UrlTrait
{
    private function makeURL(string $url): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($url)->lower()->toString();
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
