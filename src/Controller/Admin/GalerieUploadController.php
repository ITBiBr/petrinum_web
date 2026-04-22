<?php

namespace App\Controller\Admin;


use App\Entity\Akce;
use App\Entity\Foto;
use App\Entity\FotoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GalerieUploadController extends AbstractController
{
    public function __construct(private readonly array $mapEntity, private readonly array $mapFotoSetter)
    {

    }
    #[Route('/admin/upload/foto/upload/{entity}/{id}', name: 'admin_upload_foto_upload', methods: ['POST'])]
    public function upload(
        string $entity,
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(['error' => 'No file'], 400);
        }

        $class = $this->mapEntity[$entity] ?? null;
        $setter = $this->mapFotoSetter[$entity] ?? null;

        if (!$class || !$setter) {
            return $this->json(['error' => 'Unknown entity'], 400);
        }

        $object = $em->getRepository($class)->find($id);


        if (!$object) {
            return $this->json(['error' => 'Entity not found'], 404);
        }

        // ===== FILE SYSTEM =====
        $baseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
        $thumbDir = $baseDir . '/thumbs';

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }

        $filename = bin2hex(random_bytes(8)) . '.' . $file->guessExtension();

        $file->move($baseDir, $filename);
        $finalPath = $baseDir . '/' . $filename;

        // zmenšit originál a vytvořit thumbnail
        $manager = new ImageManager(new Driver());

        $manager->read($finalPath)
            ->scaleDown(2000, 2000)
            ->save($finalPath);

        $manager->read($finalPath)
            ->scaleDown(200, 200)
            ->save($thumbDir . '/' . $filename);

        // uložit do DB
        $foto = new Foto();
        $foto->setSoubor('uploads/images/' . $filename);
        //$foto->setNazev($file->getClientOriginalName());
        $foto->setNazev('');
        $foto->setPosition(0);

        // dynamické přiřazení
        $foto->{$setter}($object);

        $em->persist($foto);
        $em->flush();

        return $this->json([
            'id' => $foto->getId(),
            'name' => '('.$file->getClientOriginalName().')',
            'url' => '/uploads/images/' . $filename,
            'thumbUrl' => '/uploads/images/thumbs/' . $filename,
            'size' => filesize($finalPath),
        ]);
    }



    #[Route('/admin/upload/foto/remove/{id}', name: 'admin_upload_foto_remove', methods: ['DELETE'])]
    public function delete(Foto $foto, EntityManagerInterface $em): JsonResponse
    {
        $baseDir = $this->getParameter('kernel.project_dir') . '/public/';

        // originál
        $filePath = $baseDir . $foto->getSoubor();

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // 🔥 thumbnail
        $thumbPath = $baseDir . 'uploads/images/thumbs/' . basename($foto->getSoubor());

        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        $em->remove($foto);
        $em->flush();

        return $this->json(['success' => true]);
    }


    #[Route('/admin/upload/foto/list/{entity}/{id}', name: 'admin_upload_foto_list', methods: ['GET'])]
    public function list(
        string $entity,
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {


        if (!isset($this->mapEntity[$entity])) {
            return $this->json(['error' => 'Unknown entity'], 400);
        }

        $object = $em->getRepository($this->mapEntity[$entity])->find($id);

        if (!$object) {
            return $this->json([]);
        }

        if (!$object instanceof FotoInterface) {
            return $this->json(['error' => 'Entita nepodporuje fotky'], 400);
        }

        $fotos = $object->getFotos();

        $projectDir = $this->getParameter('kernel.project_dir');

        $data = [];

        foreach ($fotos as $foto) {
            $path = $foto->getSoubor();
            $fullPath = $projectDir . '/public/' . $path;

            $data[] = [
                'id' => $foto->getId(),
                'name' => $foto->getNazev(),
                'url' => '/' . $path,
                'thumbUrl' => '/uploads/images/thumbs/' . basename($path),
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            ];
        }

        return $this->json($data);
    }

    #[Route('/admin/upload/foto/reorder', name: 'admin_upload_foto_reorder', methods: ['POST'])]
    public function reorder(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $item) {
            $foto = $em->getRepository(Foto::class)->find($item['id']);
            if ($foto) {
                $foto->setPosition($item['position']);
            }
        }

        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/admin/upload/foto/rename{id}', name: 'admin_upload_foto_rename', methods: ['POST'])]
    public function rename(
        Foto $foto,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nazev'])) {
            return $this->json(['error' => 'Missing name'], 400);
        }

        $foto->setNazev($data['nazev']);
        $em->flush();

        return $this->json(['success' => true]);
    }


}
