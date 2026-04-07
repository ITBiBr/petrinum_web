<?php

namespace App\Controller\Admin;


use App\Entity\Foto;
use App\Entity\Galerie;
use App\Repository\GalerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GalerieUploadController extends AbstractController
{


    #[Route('/admin/upload-foto/{galerieId}', name: 'admin_upload_foto', methods: ['POST'])]
    public function upload(
        Request $request,
        EntityManagerInterface $em,
        GalerieRepository $galerieRepo
    ): JsonResponse {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(['error' => 'No file'], 400);
        }

        $galerie = $galerieRepo->find($request->get('galerieId'));

        if (!$galerie) {
            return $this->json(['error' => 'Galerie not found'], 404);
        }

        $baseDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
        $thumbDir = $baseDir . '/thumbs';

        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }

        $filename = bin2hex(random_bytes(8)) . '.' . $file->guessExtension();

        // uložit originál
        $file->move($baseDir, $filename);

        // 🔥 vytvořit thumbnail
        $manager = new ImageManager(new Driver());

        $image = $manager->read($baseDir . '/' . $filename)
            ->cover(300, 300); //

        $image->save($thumbDir . '/' . $filename);

        // uložit do DB
        $foto = new Foto();
        $foto->setSoubor('uploads/images/' . $filename);
        $foto->setNazev($file->getClientOriginalName());
        $foto->setGalerie($galerie);

        $em->persist($foto);
        $em->flush();

        return $this->json([
            'id' => $foto->getId(),
            'name' => $filename,
            'url' => '/uploads/images/' . $filename,
            'thumbUrl' => '/uploads/images/thumbs/' . $filename, // 🔥
        ]);
    }



    #[Route('/admin/delete-foto/{id}', name: 'admin_delete_foto', methods: ['DELETE'])]
    public function delete(Foto $foto, EntityManagerInterface $em): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $foto->getSoubor();

        if (file_exists($filePath)) {
            unlink($filePath);
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
                'url' => '/'.$foto->getSoubor(),
                'size' => 12345, // klidně fake, Dropzone to neřeší
            ];
        }

        return $this->json($data);
    }


}
