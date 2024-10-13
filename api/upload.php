<?php
header("Access-Control-Allow-Origin: *"); // Permitir cualquier origen
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Authorization, Content-Type"); // Encabezados permitidos

/**
 * Función para validar el token de autorización desde el archivo tokens.txt.
 *
 * @param string $token El token enviado en la solicitud
 * @return bool Verdadero si el token es válido, falso en caso contrario
 */
function is_token_valid($token)
{
    $tokens_file = __DIR__ . '/tokens.txt';

    if (!file_exists($tokens_file)) {
        return false; // El archivo de tokens no existe
    }

    // Leer el archivo tokens.txt y validar el token
    $tokens = file($tokens_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Limpiar espacios en blanco en cada token
    $tokens = array_map('trim', $tokens);

    return in_array(trim($token), $tokens);
}

/**
 * Función para generar un UUID.
 *
 * @return string Un UUID
 */
function generate_uuid()
{
    return bin2hex(random_bytes(16)); // Generar un UUID (no estándar)
}

/**
 * Función para subir un archivo a un directorio específico.
 *
 * @param array $file El archivo subido ($_FILES['upload'])
 * @param string $folder El directorio donde se guardará el archivo
 * @return array Un array que contiene el estado de la operación y mensajes
 */
function upload_image($file, $folder)
{
    // Directorio donde se guardarán las imágenes
    $target_dir = __DIR__ . '/../images/' . $folder . '/';

    // Crear el directorio si no existe
    if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
        return ['success' => false, 'status' => 'error', 'message' => 'Error al crear el directorio'];
    }

    // Obtener la extensión del archivo
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Usar el nombre original del archivo para guardarlo
    $original_file_name = pathinfo($file['name'], PATHINFO_FILENAME); // Nombre del archivo sin extensión
    $new_file_name = basename($file['name']); // Nombre del archivo
    $target_file = $target_dir . $new_file_name; // Ruta completa del archivo

    // Verificar si el archivo es una imagen
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'status' => 'error', 'message' => 'El archivo no es una imagen'];
    }

    // Si el archivo ya existe, generar un nuevo nombre con un UUID
    while (file_exists($target_file)) {
        $uuid = generate_uuid();
        $new_file_name = $original_file_name . '_' . $uuid . '.' . $imageFileType; // Nombre nuevo
        $target_file = $target_dir . $new_file_name; // Nueva ruta completa del archivo
    }

    // Intentar mover el archivo subido al directorio objetivo con el nombre original o nuevo
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Construir la URL donde se puede acceder a la imagen
        $url = '/images/' . $folder . '/' . $new_file_name; // URL relativa

        return [
            'success' => true,
            'uploaded' => true,
            'message' => 'Imagen subida exitosamente',
            'url' => $url // URL de la imagen
        ];
    } else {
        return ['uploaded' => false, 'message' => 'Error al subir la imagen'];
    }
}

// Ejemplo de uso (solo cuando se recibe una solicitud POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    // Validar el token de autorización
    $headers = getallheaders();
    $authorization_header = $headers['Authorization'] ?? null;

    // Validar el token
    if (!$authorization_header || !is_token_valid($authorization_header)) {
        http_response_code(401); // Código de no autorizado
        echo json_encode(['success' => false, 'status' => 'error', 'message' => 'Token de autorización inválido']);
        exit;
    }

    // Obtener la carpeta y el nombre del archivo desde los parámetros de consulta
    $folder = isset($_GET['folder']) ? urldecode($_GET['folder']) : 'default'; // Carpeta, con valor por defecto 'default'

    // Llamada a la función upload_image() y pasando el archivo
    $result = upload_image($_FILES['upload'], $folder);

    // Devolver la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // En caso de que no sea una solicitud válida
    http_response_code(400);
    echo json_encode(['success' => false, 'status' => 'error', 'message' => 'Solicitud no válida']);
}
