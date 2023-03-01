<?php
session_start();

use App\Tablas\Factura;

require '../vendor/autoload.php';

//Comprobar que el usuario esta logueado.
if (!($usuario = \App\Tablas\Usuario::logueado())) {
    return volver();
}

$id = obtener_get('id');

//Comprobar si se ha recogido el id de la factura.
if (!isset($id)) {
    return volver();
}

$pdo = conectar();

//El metodo obtener es de la clase modelo y en esta ocasión se realiza con Factura.
//Recoge la factura con el id indicado.
$factura = Factura::obtener($id, $pdo);

//Comprobar si se ha recogido la factura indicada.
if (!isset($factura)) {
    return volver();
}

//Comprobar si la factura es del usuario logueado.
if ($factura->getUsuarioId() != $usuario->id) {
    return volver();
}

//Tabla
$filas_tabla = '';
$total = 0;

foreach ($factura->getLineas($pdo) as $linea) { //Duda funcionamiento.
    //Obtención de datos.
    $articulo = $linea->getArticulo();
    $codigo = $articulo->getCodigo();
    $descripcion = $articulo->getDescripcion();
    $cantidad = $linea->getCantidad();
    $precio = $articulo->getPrecio();
    $descuento = ($precio * $articulo->getDescuento()) / 100;
    $precio_final = $precio - $descuento;

    //Operaciones con datos.
    $total_linea = $precio_final * $cantidad;
    $iva = $total_linea * 0.21;
    $importe = $total_linea + $iva;
    $total += $importe;

    //Formateado de datos.
    $total_linea = dinero($total_linea);
    $iva = dinero($iva);
    $importe = dinero($importe);

    $filas_tabla .= <<<EOF
        <tr>
            <td>$codigo</td>
            <td>$descripcion</td>
            <td>$cantidad</td>
            <td>$total_linea</td>
            <td>$iva</td>
            <td>$importe</td>
        </tr>
    EOF;
}

//Formato monetario al total.
$total = dinero($total);

$res = <<<EOT
<p>Factura número: {$factura->id}</p>

<table border="1" class="font-sans mx-auto">
    <tr>
        <th>Código</th>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>IVA</th>
        <th>Importe</th>
    </tr>
    <tbody>
        $filas_tabla
    </tbody>
</table>

<p>Total: $total</p>
EOT;

// Create an instance of the class:
$mpdf = new \Mpdf\Mpdf();

// Write some HTML code:
$mpdf->WriteHTML(file_get_contents('css/output.css'), \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($res, \Mpdf\HTMLParserMode::HTML_BODY);

// Output a PDF file directly to the browser
$mpdf->Output();
