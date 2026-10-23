<?php
function CalcularIVA (Float $precio, float $tasa = 0.19):float{
    return round ($precio * (1 + $tasa), 2);
    }

function formatearPrecio(float $valor): string {
    return '$' . number_format($valor, 0,',','.');
}

echo formatearPrecio(CalcularIVA(50000)) . "\n";
echo formatearPrecio(CalcularIVA(50000, 0.05)) . "\n"

?>