<?php

$inputFile  = "fotos.sql";
$outputFile = "fotos.txt";

// otevření souborů
$in  = fopen($inputFile, "r");
$out = fopen($outputFile, "w");

if (!$in || !$out) {
    die("Chyba při otevírání souborů.");
}

while (($line = fgets($in)) !== false) {

    // hledáme jen řádky s INSERT INTO `fotos`
    if (strpos($line, "INSERT INTO `fotos`") !== false) {

        // vytáhneme hodnoty mezi VALUES (...)
        preg_match("/VALUES\s*\((.*)\);/i", $line, $matches);

        if (!empty($matches[1])) {

            // rozdělíme hodnoty (bezpečně přes CSV parser)
            $values = str_getcsv($matches[1], ',', "'");

            // mapování:
            // 0 = id (např. 219)
            // 1 = aktuality_id (např. 60)
            // 3 = nazev (např. výprava)

            $id            = $values[0];
            $aktuality_id  = $values[1];
            $nazev         = $values[3];

            // vytvoření nové cesty
            $soubor = "uploads/images/" . $id . ".jpg";

            // nový INSERT
            $newLine = "INSERT INTO `foto` (`nazev`, `soubor`, `position`, `akce_id`) VALUES "
                     . "('$nazev', '$soubor', '0', '$aktuality_id');\n";

            fwrite($out, $newLine);
        }

    } else {
        // ostatní řádky jen přepíšeme
        fwrite($out, $line);
    }
}

fclose($in);
fclose($out);

echo "Hotovo! Výstup uložen do: $outputFile";

?>
