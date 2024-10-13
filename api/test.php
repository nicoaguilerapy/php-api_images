<?php
// Establecer el encabezado para indicar que se está devolviendo JSON
header('Content-Type: application/json');

// Obtener la fecha y hora actuales en el formato dd/mm/yyyy hh:mm
$date = date('d/m/Y H:i');

// Crear un array para la respuesta
$response = [
    'success' => true,
    'message' => "Servicio de Imágenes en línea $date"
];

// Devolver la respuesta como JSON
echo json_encode($response);
?>
