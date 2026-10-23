<?php
declare(strict_types=1);
function VerificarNumero(int $a):void {
    if ($a % 2 === 0) {
        echo "El número $a es par.\n";
    } else {
        echo "El número $a es impar.\n";
    }
}
VerificarNumero(8);
VerificarNumero(5);
VerificarNumero(12);