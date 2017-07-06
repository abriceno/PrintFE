<?php

$cada = 4;
$contador=0;
for ($i=0;$i<256;$i++) {
    $contador++;
    echo "CHR($i) = " . chr($i)." = \u00".dechex($i). " , ";
    if ($contador==$cada) {
        echo "\n";
        $contador = 0;
    }
}
echo "\n";
