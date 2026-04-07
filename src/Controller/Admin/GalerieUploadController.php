<?php

namespace App\Controller\Admin;


use App\Entity\Foto;
use App\Entity\Galerie;
use App\Repository\GalerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GalerieUploadController extends AbstractController
{


    #[Route('/admin/upload-foto/{id}', name: 'admin_upload_foto', methods: ['POST'])]
    public function upload(
        Request $request,
        EntityManagerInterface $em,
        Galerie $galerie
    ): JsonResponse {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(['error' => 'No file'], 400);
        }


        $baseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
        $thumbDir = $baseDir . '/thumbs';

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }

        if (!is_writable($baseDir)) {
            return $this->json([
                'error' => 'Base dir not writable',
                'dir' => $baseDir
            ], 500);
        }

        $filename = bin2hex(random_bytes(8)) . '.' . $file->guessExtension();

        // uložit originál
        $file->move($baseDir, $filename);

        // 🔥 vytvořit thumbnail
        $manager = new ImageManager(new Driver());

        $image = $manager->read($baseDir . '/' . $filename)
            ->scaleDown(200, 200);




        $image->save($thumbDir . '/' . $filename);

        // uložit do DB
        $foto = new Foto();
        $foto->setSoubor('uploads/images/' . $filename);
        $foto->setNazev($file->getClientOriginalName());
        $foto->setGalerie($galerie);
        $foto->setPosition(0);

        $em->persist($foto);
        $em->flush();

        return $this->json([
            'id' => $foto->getId(),
            'name' => $filename,
            'url' => '/uploads/images/' . $filename,
            'thumbUrl' => '/uploads/images/thumbs/' . $filename,
        ]);
    }



    #[Route('/admin/delete-foto/{id}', name: 'admin_delete_foto', methods: ['DELETE'])]
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


    #[Route('/admin/galerie/{id}/fotos', name: 'admin_galerie_fotos', methods: ['GET'])]
    public function list(Galerie $galerie): JsonResponse
    {
        $data = [];

        foreach ($galerie->getFotos() as $foto) {
            $data[] = [
                'id' => $foto->getId(),
                'name' => $foto->getNazev(),
                'url' => '/' . $foto->getSoubor(),
                'thumbUrl' => '/uploads/images/thumbs/' . basename($foto->getSoubor()),
                'size' => filesize($this->getParameter('kernel.project_dir') . '/public/' . $foto->getSoubor()),
            ];

        }

        return $this->json($data);
    }

    #[Route('/admin/foto/reorder', name: 'admin_foto_reorder', methods: ['POST'])]
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

}
