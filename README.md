# # Proyecto pensado para ser utilizado con Django con HttpStorage

Funcionamiento de la API
La API cuenta con dos endpoints principales: /upload/ y /image/.

1. Subida de imágenes - /upload/
Este endpoint permite subir imágenes al servidor, organizándolas en carpetas específicas según el parámetro folder proporcionado en la URL.

Método: POST

Parámetros:

folder: El nombre de la carpeta donde se guardará la imagen (en la URL).
name: El nombre del archivo a guardar.
Archivo a subir (upload en el cuerpo de la solicitud).
Authorization: Se debe incluir un token válido en los headers de la solicitud.
Respuesta: Retorna un objeto JSON con información sobre el éxito o error de la operación, incluyendo la URL de la imagen subida si la operación es exitosa.

/upload/?folder={folder}&name={name}

2. Acceso a imágenes - /image/
Este endpoint permite acceder a las imágenes previamente subidas, proporcionando la URL directa para visualizarlas o descargarlas.

Método: GET

Parámetros:

folder: El nombre de la carpeta donde está almacenada la imagen.
name: El nombre del archivo de imagen.
Respuesta: Muestra la imagen solicitada o devuelve un error si no existe.

/image/?folder={folder}&name={name}
