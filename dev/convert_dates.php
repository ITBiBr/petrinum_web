<?php

$inputFile  = "news_dates.sql";
$outputFile = "news_dates.txt";

$in  = fopen($inputFile, "r");
$out = fopen($outputFile, "w");

if (!$in || !$out) {
    die("Chyba při otevírání souborů.");
}

while (($line = fgets($in)) !== false) {

    // hledáme jen řádky s INSERT INTO `news`
    if (strpos($line, "INSERT INTO `news_dates`") !== false) {

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
            $id_news  = $values[1];
            $date = $values[2];
        

            $url     = '';

            // nový INSERT
            $newLine = " UPDATE `akce` SET `datum` = '$date' WHERE `akce`.`id` = '$id_news';\n";
                

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
