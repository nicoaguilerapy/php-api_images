<?php
header("Access-Control-Allow-Origin: *"); // Permitir cualquier origen
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Métodos permitidos
header("Content-Type: application/json"); // Tipo de contenido de la respuesta

/**
 * Función para verificar si una imagen existe en el servidor.
 *
 * @param string $folder Carpeta donde se busca la imagen
 * @param string $name Nombre de la imagen
 * @return array Un array que contiene el estado y mensaje de la operación
 */
function check_image_exists($folder, $name)
{
    // Construir la ruta completa de la imagen
    $image_path = __DIR__ . '/../images/' . $folder . '/' . $name;

    // Verificar si el archivo existe
    if (file_exists($image_path)) {
        // Verificar si es una imagen válida
        $image_info = getimagesize($image_path);
        if ($image_info) {
            return [
                'status' => 'success',
                'exists' => true,
                'message' => 'La imagen existe',
                'url' => '/images/' . $folder . '/' . $name // URL relativa
            ];
        } else {
            http_response_code(400);
            return [
                'status' => 'error',
                'exists' => false,
                'message' => 'El archivo existe pero no es una imagen válida'
            ];
        }
    } else {
        http_response_code(400);
        return [
            'status' => 'error',
            'exists' => false,
            'message' => 'Imagen no encontrada'
        ];
    }
}

// Ejemplo de uso (solo cuando se recibe una solicitud GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener la carpeta y el nombre de la imagen desde los parámetros de consulta
    $folder = isset($_GET['folder']) ? urldecode($_GET['folder']) : 'default'; // Carpeta, con valor por defecto 'default'
    $name = isset($_GET['name']) ? urldecode($_GET['name']) : null; // Nombre de la imagen

    if ($name) {
        // Llamada a la función para verificar la existencia de la imagen
        $result = check_image_exists($folder, $name);
        echo json_encode($result);
    } else {
        // Error si no se proporciona el nombre de la imagen
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nombre de la imagen no proporcionado']);
    }
} else {
    // En caso de que no sea una solicitud válida
    http_response_code(405); // Método no permitido
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>
