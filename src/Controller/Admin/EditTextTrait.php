<?php

namespace App\Controller\Admin;

trait EditTextTrait
{
    public function addNbsp(?string $text): string
    {
        // 1) Jednopísmenné předložky a spojky (a, i, k, s, v, z, o, u)
        // 2) Zkratka "P." (např. P. Novák)
        $patterns = [
            // jednopísmenné spojky/předložky
            '/(?<=\s|^)([KkSsVvZzOoUuAaIi])\s+(?=\S)/u',
            // zkratka P.
            '/(?<=\bP\.)\s+(?=\p{Lu})/u',  // mezi "P." a velkým písmenem
            // Číslo + tečka + mezera → např. "1. ledna"
            '/(\d+)\.\s+(?=\p{L})/u',

        ];

        $replacements = [
            '$1&nbsp;',
            '&nbsp;',
            '$1.&nbsp;',
        ];
        return preg_replace($patterns, $replacements, $text);
    }
}
