<?php

namespace App\EventListener;

use App\Entity\Aktuality;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;

#[AsDoctrineListener(event: 'preRemove')]
final class AktualityDeleteListener
{
    public function preRemove(PreRemoveEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof Aktuality) {
            return;
        }

        $baseDir = dirname(__DIR__, 2) . '/public/';

        foreach ($entity->getFotos() as $foto) {
            $filePath = $baseDir . $foto->getSoubor();
            $thumbPath = $baseDir . 'uploads/images/thumbs/' . basename($foto->getSoubor());

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
    }
}

