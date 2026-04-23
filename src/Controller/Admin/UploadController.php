<?php

namespace App\Controller\Admin;

use App\Entity\FotoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UploadController extends AbstractController
{
    public function __construct(private readonly array $mapEntity, private readonly array $mapSetter, private readonly array $mapFileEntity, private readonly array $mapUploadDir, private readonly array $mapGetter)
    {

    }

    #[Route('/admin/upload/{data_type}/upload/{entity}/{id}', name: 'admin_upload_upload', methods: ['POST'])]
    public function upload(
        string $entity,
        string $data_type,
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $config = [
            'fileEntity' => $this->mapFileEntity[$data_type] ?? null,
            'uploadDir'  => $this->mapUploadDir[$data_type] ?? 'files',
            'class' => $this->mapEntity[$entity] ?? null,
            'setter' => $this->mapSetter[$entity] ?? null,
        ];
        $file = $request->files->get('file');
        if (!$file) {
            return $this->json(['error' => 'No file'], 400);
        }


        if (!$config['class'] || !$config['setter'] || !$config['fileEntity']) {
            return $this->json(['error' => 'Unknown entity'], 400);
        }

        $object = $em->getRepository($config['class'])->find($id);


        if (!$object) {
            return $this->json(['error' => 'Entity not found'], 404);
        }

        // ===== FILE SYSTEM =====
        $baseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/'.$config['uploadDir'];

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        if ($data_type == 'image') {
            $thumbDir = $baseDir . '/thumbs';

            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0777, true);
            }
        }

        $filename = bin2hex(random_bytes(8)) . '.' . $file->guessExtension();

        $file->move($baseDir, $filename);
        $finalPath = $baseDir . '/' . $filename;

        if ($data_type == 'image') {
            // zmenšit originál a vytvořit thumbnail
            $manager = new ImageManager(new Driver());

            $manager->read($finalPath)
                ->scaleDown(2000, 2000)
                ->save($finalPath);

            $manager->read($finalPath)
                ->scaleDown(200, 200)
                ->save($thumbDir . '/' . $filename);
        }

        // uložit do DB
        $new_file = new $config['fileEntity'];
        $new_file->setSoubor('uploads/'. $config['uploadDir'].'/' . $filename);
        //$foto->setNazev($file->getClientOriginalName());
        $new_file->setNazev('');
        $new_file->setPosition(0);

        // dynamické přiřazení
        $new_file->{$config['setter']}($object);

        $em->persist($new_file);
        $em->flush();

        $response = [
            'id' => $new_file->getId(),
            'name' => '(' . $file->getClientOriginalName() . ')',
            'url' => 'uploads/'. $config['uploadDir'].'/' . $filename,
        ];

        if ($data_type == 'image') {
            $response['thumbUrl'] = '/uploads/'. $config['uploadDir'].'/thumbs/' . $filename;
            $response['size'] = filesize($finalPath);
        }
        else{
            $response['thumbUrl'] = '/icon/'. $file->guessExtension();
        }

        return $this->json($response);
    }



    #[Route('/admin/upload/{data_type}/remove/{id}', name: 'admin_upload_remove', methods: ['DELETE'])]
    public function delete(string $data_type, int $id, EntityManagerInterface $em): JsonResponse
    {
        $baseDir = $this->getParameter('kernel.project_dir') . '/public/';
        $fileEntity = $em->getRepository($this->mapFileEntity[$data_type] ?? null)->find($id);

        // originál
        $filePath = $baseDir . $fileEntity->getSoubor();

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($data_type == 'image') {
            // thumbnail
            $thumbPath = $baseDir . 'uploads/' . $this->mapUploadDir[$data_type] . '/thumbs/' . basename($fileEntity->getSoubor());

            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
        $em->remove($fileEntity);
        $em->flush();

        return $this->json(['success' => true]);
    }


    #[Route('/admin/upload/{data_type}/list/{entity}/{id}', name: 'admin_upload_list', methods: ['GET'])]
    public function list(
        string $entity,
        string $data_type,
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {


        if (!isset($this->mapEntity[$entity])) {
            return $this->json(['error' => 'Unknown entity'], 400);
        }

        $object = $em->getRepository($this->mapEntity[$entity])?->find($id);

        if (!$object) {
            return $this->json([]);
        }

        if (!$object instanceof FotoInterface) {
            return $this->json(['error' => 'Entita nepodporuje fotky'], 400);
        }
        $getter = $this->mapGetter[$data_type];
        $files = $object->$getter();

        $projectDir = $this->getParameter('kernel.project_dir');

        $data = [];

        foreach ($files as $file) {
            $path = $file->getSoubor();
            $fullPath = $projectDir . '/public/' . $path;



            $item = [
                'id' => $file->getId(),
                'name' => $file->getNazev(),
                'url' => '/' . $path,
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
            ];

            if ($data_type === 'image') {
                $item['thumbUrl'] = '/uploads/'.$this->mapUploadDir[$data_type].'/thumbs/' . basename($path);
            }

            $data[] = $item;
        }

        return $this->json($data);
    }

    #[Route('/admin/upload/{data_type}/reorder', name: 'admin_upload_reorder', methods: ['POST'])]
    public function reorder(Request $request, EntityManagerInterface $em, string $data_type): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $item) {
            $fileEntity = $em->getRepository($this->mapFileEntity[$data_type] ?? null)?->find($item['id']);
            if ($fileEntity) {
                $fileEntity->setPosition($item['position']);
            }
        }

        $em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/admin/upload/{data_type}/rename/{id}', name: 'admin_upload_rename', methods: ['POST'])]
    public function rename(
        string $data_type,
        Request $request,
        int $id,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['nazev'])) {
            return $this->json(['error' => 'Missing name'], 400);
        }
        $fileEntity = $em->getRepository($this->mapFileEntity[$data_type] ?? null)?->find($id);
        if ($fileEntity) {
            $fileEntity->setNazev($data['nazev']);
        }
        $em->flush();

        return $this->json(['success' => true]);
    }


}
