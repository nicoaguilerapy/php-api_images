<?php

header("Access-Control-Allow-Origin: *"); // Permitir cualquier origen. Cambia '*' por el dominio específico si es necesario.
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

/**
 * Función para obtener y devolver una imagen del servidor.
 *
 * @param string $image_path La ruta completa de la imagen
 * @return void
 */
function get_image($image_path)
{
    // Verificar si el archivo existe
    if (!file_exists($image_path)) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Imagen no encontrada', 'path' => $image_path]); // Mostrar la ruta para depuración
        return;
    }

    // Obtener la extensión del archivo para determinar el tipo MIME
    $file_extension = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

    // Establecer el tipo MIME correcto según la extensión
    switch ($file_extension) {
        case 'jpg':
        case 'jpeg':
            $mime_type = 'image/jpeg';
            break;
        case 'png':
            $mime_type = 'image/png';
            break;
        case 'gif':
            $mime_type = 'image/gif';
            break;
        case 'webp':
            $mime_type = 'image/webp';
            break;
        default:
            http_response_code(415); // Unsupported Media Type
            echo json_encode(['status' => 'error', 'message' => 'Tipo de archivo no soportado']);
            return;
    }

    // Enviar la cabecera adecuada para la imagen
    header('Content-Type: ' . $mime_type);
    header('Content-Length: ' . filesize($image_path));

    // Leer y devolver el archivo de imagen
    readfile($image_path);
    exit;
}

// Ejemplo de uso (cuando se recibe una solicitud GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener la carpeta y el nombre de la imagen desde los parámetros de la URL
    $folder = isset($_GET['folder']) ? urldecode($_GET['folder']) : null; // Carpeta
    $name = isset($_GET['name']) ? urldecode($_GET['name']) : null; // Nombre de la imagen

    if ($folder && $name) {
        // Construir la ruta completa de la imagen
        $image_path = __DIR__ . '/../images/' . $folder . '/' . $name;

        // Llamada a la función para obtener y devolver la imagen
        get_image($image_path);
    } else {
        // Error si no se proporcionan los parámetros necesarios
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Parámetros faltantes: folder o name']);
    }
} else {
    // En caso de que no sea una solicitud válida
    http_response_code(405); // Método no permitido
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}