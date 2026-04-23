<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FileTypeSvgController extends AbstractController
{
    #[Route('/icon/{type}', name: 'icon_svg', requirements: ['type' => '[A-Za-z]{1,4}'], methods: ['GET'])]
    public function icon(string $type): Response
    {
        $type = strtoupper($type);

        $svg = <<<SVG
                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2h24l12 12v48H14z" fill="#f5f5f5" stroke="#333" stroke-width="2"/>
                    <path d="M38 2v12h12" fill="#e0e0e0" stroke="#333" stroke-width="2"/>
                    <text x="32" y="40" font-family="Arial, sans-serif" font-size="10" text-anchor="middle" fill="#333">
                        {$type}
                    </text>
                </svg>
                SVG;

        return new Response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
