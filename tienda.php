<?php
declare(strict_types=1);

// =====================================================
// Taller PHP: Mini tienda con funciones y manejo de errores
// Ejecutar con: php tienda.php
// =====================================================

function titulo(string $texto): void {
    echo "\n=== $texto ===\n";
}

// -----------------------------------------------------
// PARTE 1. Primeras funciones
// -----------------------------------------------------
titulo('Parte 1. Primeras funciones');

function calcularIVA(float $precio, float $tasa = 0.19): float {
    return round($precio * (1 + $tasa), 2);
}

function formatearPrecio(float $valor): string {
    return '$' . number_format($valor, 0, ',', '.');
}

echo formatearPrecio(calcularIVA(50000)) . "\n";        // IVA del 19 %
echo formatearPrecio(calcularIVA(50000, 0.05)) . "\n";  // IVA del 5 %

// Tu turno: saludarCliente
function saludarCliente(string $nombre = "cliente"): string {
    return "Bienvenido a la tienda, $nombre";
}

echo saludarCliente() . "\n";
echo saludarCliente("Laura") . "\n";

// -----------------------------------------------------
// PARTE 2. Parámetros
// -----------------------------------------------------
titulo('Parte 2. Parámetros');

// Argumentos nombrados
function aplicarDescuento(float $precio, float $porcentaje = 0): float {
    return $precio - ($precio * $porcentaje / 100);
}

echo formatearPrecio(aplicarDescuento(precio: 80000, porcentaje: 10)) . "\n";

// Número variable de argumentos
function totalCarrito(float ...$precios): float {
    return array_sum($precios);
}

echo formatearPrecio(totalCarrito(15000, 22000, 8000)) . "\n";

// Por referencia
function descontarStock(int &$stock, int $cantidad): void {
    $stock -= $cantidad;
}

$stockCuadernos = 10;
descontarStock($stockCuadernos, 3);
echo "Quedan $stockCuadernos cuadernos\n";

// Tu turno: promedioPrecios (la versión con validación está en la Parte 4)
function promedioPrecios(float ...$precios): float {
    if (count($precios) === 0) {
        throw new InvalidArgumentException("Debes pasar al menos un precio.");
    }
    return array_sum($precios) / count($precios);
}

echo "Promedio: " . formatearPrecio(promedioPrecios(8000, 1500, 95000, 12000, 30000)) . "\n";

// -----------------------------------------------------
// PARTE 3. Funciones predefinidas
// -----------------------------------------------------
titulo('Parte 3. Predefinidas');

$productos = [
    ['nombre' => 'Cuaderno', 'precio' => 8000,  'stock' => 20],
    ['nombre' => 'Lápiz',    'precio' => 1500,  'stock' => 0],
    ['nombre' => 'Mochila',  'precio' => 95000, 'stock' => 4],
];

$nombres = array_column($productos, 'nombre');
echo "Catálogo: " . implode(', ', $nombres) . "\n";
echo "Total de productos: " . count($productos) . "\n";

$disponibles = array_filter($productos, fn($p) => $p['stock'] > 0);
echo "Con stock: " . count($disponibles) . "\n";

$precios = array_column($productos, 'precio');
echo "El más caro cuesta " . formatearPrecio(max($precios)) . "\n";

// Tu turno: array_map y strtoupper
$preciosConIVA = array_map(fn($p) => calcularIVA((float) $p), $precios);
echo "Precios con IVA: " . implode(', ', array_map('formatearPrecio', $preciosConIVA)) . "\n";
echo "Nombres: " . implode(', ', array_map('mb_strtoupper', $nombres)) . "\n";

// -----------------------------------------------------
// PARTE 4. Excepciones
// -----------------------------------------------------
titulo('Parte 4. Excepciones');

class ProductoAgotadoException extends Exception {
    public function __construct(string $producto) {
        parent::__construct("No hay unidades suficientes de «{$producto}».");
    }
}

function buscarProducto(array $productos, string $nombre): array {
    foreach ($productos as $p) {
        if ($p['nombre'] === $nombre) {
            return $p;
        }
    }
    throw new InvalidArgumentException("El producto «{$nombre}» no existe.");
}

function comprar(array $productos, string $nombre, int $cantidad): float {
    if ($cantidad <= 0) {
        throw new RangeException("La cantidad debe ser mayor que cero.");
    }
    $producto = buscarProducto($productos, $nombre);
    if ($producto['stock'] < $cantidad) {
        throw new ProductoAgotadoException($nombre);
    }
    return calcularIVA($producto['precio'] * $cantidad);
}

try {
    echo formatearPrecio(comprar($productos, 'Lápiz', 1)) . "\n";
} catch (ProductoAgotadoException $e) {
    echo "Aviso: " . $e->getMessage() . "\n";
} catch (InvalidArgumentException | RangeException $e) {
    echo "Pedido inválido: " . $e->getMessage() . "\n";
} finally {
    echo "Consulta finalizada.\n";
}

// Tu turno: promedioPrecios sin argumentos
try {
    echo promedioPrecios() . "\n";
} catch (InvalidArgumentException $e) {
    echo "Error en promedio: " . $e->getMessage() . "\n";
}

// -----------------------------------------------------
// PARTE 5. Todo junto
// -----------------------------------------------------
titulo('Parte 5. Todo junto');

$pedidos = [
    ['Cuaderno', 2],
    ['Lápiz',    1],
    ['Borrador', 3],
    ['Mochila',  0],
    ['Mochila',  1],
];

$ventaTotal = 0;

foreach ($pedidos as [$nombre, $cantidad]) {
    try {
        $valor = comprar($productos, $nombre, $cantidad);
        $ventaTotal += $valor;
        echo "OK  - $cantidad x $nombre: " . formatearPrecio($valor) . "\n";
    } catch (ProductoAgotadoException $e) {
        echo "SIN STOCK - " . $e->getMessage() . "\n";
    } catch (InvalidArgumentException | RangeException $e) {
        echo "ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\nTotal vendido: " . formatearPrecio($ventaTotal) . "\n";