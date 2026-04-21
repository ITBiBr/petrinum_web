<?php

$inputFile  = "news.sql";
$outputFile = "news.txt";

$in  = fopen($inputFile, "r");
$out = fopen($outputFile, "w");

if (!$in || !$out) {
    die("Chyba při otevírání souborů.");
}

while (($line = fgets($in)) !== false) {

    // hledáme jen řádky s INSERT INTO `news`
    if (strpos($line, "INSERT INTO `news`") !== false) {

        // vytáhneme hodnoty mezi VALUES (...)
        preg_match("/VALUES\s*\((.*)\);/i", $line, $matches);

        if (!empty($matches[1])) {

            // bezpečné rozparsování CSV hodnot
            $values = str_getcsv($matches[1], ',', "'");

            // mapování:
            // 0 = id
            // 1 = datum
            // 2 = titulek
            // 3 = text1
            // 4 = text2
            // 5 = url (NULL › prázdný string)

            $id     = $values[0];
            $datum  = $values[1];
            $titulek = addslashes($values[2]);
            $text1   = addslashes($values[3]);
            $text2   = addslashes($values[4]);

            $url     = '';

            // nový INSERT
            $newLine = "INSERT INTO `akce` (`id`, `titulek`, `datum`, `obsah`, `obsah_pokracovani`, `url`) "
         . "VALUES ($id, '$titulek', '$datum', '$text1', '$text2', '$url');\n";


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
