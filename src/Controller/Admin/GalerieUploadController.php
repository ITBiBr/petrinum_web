<?php

namespace App\Controller\Admin;



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
    public function __construct(private readonly array $mapEntity, private readonly array $mapSetter, private readonly array $mapFileEntity, private readonly array $mapUploadDir)
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
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(['error' => 'No file'], 400);
        }

        $class = $this->mapEntity[$entity] ?? null;
        $setter = $this->mapSetter[$entity] ?? null;
        $fileEntity = $this->mapFileEntity[$data_type] ?? null;
        $uploadDir = $this->mapUploadDir[$data_type] ?? 'files';

        if (!$class || !$setter || !$fileEntity) {
            return $this->json(['error' => 'Unknown entity'], 400);
        }

        $object = $em->getRepository($class)->find($id);


        if (!$object) {
            return $this->json(['error' => 'Entity not found'], 404);
        }

        // ===== FILE SYSTEM =====
        $baseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/'.$uploadDir;

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
        $new_file = new $fileEntity;
        $new_file->setSoubor('uploads/'. $uploadDir.'/' . $filename);
        //$foto->setNazev($file->getClientOriginalName());
        $new_file->setNazev('');
        $new_file->setPosition(0);

        // dynamické přiřazení
        $new_file->{$setter}($object);

        $em->persist($new_file);
        $em->flush();

        $response = [
            'id' => $new_file->getId(),
            'name' => '(' . $file->getClientOriginalName() . ')',
            'url' => 'uploads/'. $uploadDir.'/' . $filename,
        ];

        if ($data_type == 'image') {
            $response['thumbUrl'] = '/uploads/'. $uploadDir.'/thumbs/' . $filename;
            $response['size'] = filesize($finalPath);
        }

        return $this->json($response);
    }



    #[Route('/admin/upload/{data_type}/remove/{id}', name: 'admin_upload_remove', methods: ['DELETE'])]
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


    #[Route('/admin/upload/{data_type}/list/{entity}/{id}', name: 'admin_upload_list', methods: ['GET'])]
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

    #[Route('/admin/upload/{data_type}/reorder', name: 'admin_upload_reorder', methods: ['POST'])]
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

    #[Route('/admin/upload/{data_type}/rename/{id}', name: 'admin_upload_rename', methods: ['POST'])]
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
