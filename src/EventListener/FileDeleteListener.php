<?php

namespace App\EventListener;

use App\Entity\FotoInterface;
use App\Entity\PrilohyInterface;
use App\Entity\Zamestnanci;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreRemoveEventArgs;

#[AsDoctrineListener(event: 'preRemove')]
final class FileDeleteListener
{
    public function preRemove(PreRemoveEventArgs $event): void
    {
        $entity = $event->getObject();

        if (
            !$entity instanceof FotoInterface &&
            !$entity instanceof PrilohyInterface &&
            !$entity instanceof Zamestnanci
        ) {
            return;
        }

        $baseDir = dirname(__DIR__, 2) . '/public/';

        if ($entity instanceof FotoInterface) {
            foreach ($entity->getFotos() as $file) {
                $this->deleteFile($baseDir . $file->getSoubor());
                $this->deleteFile(
                    $baseDir . 'uploads/images/thumbs/' . basename($file->getSoubor())
                );
            }
        }

        if ($entity instanceof PrilohyInterface) {
            foreach ($entity->getPrilohies() as $file) {
                $this->deleteFile($baseDir . $file->getSoubor());
            }
        }

        if ($entity instanceof Zamestnanci) {
                $this->deleteFile($baseDir . 'images/zamestnanci/'.$entity->getFoto());
        }
    }

    private function deleteFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

}

