@vite(['resources/js/app.js'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivos con Dropzone</title>

    <!-- Dropzone CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Dropzone JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div class="flex m-5">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger" type="submit">Cerrar sesión</button>
        </form>
    </div>
    <div class="container mt-5">
        <h2 class="mb-4">Subir Archivos con Folio</h2>

        <!-- Input para folio -->
        <div class="mb-3">
            <label for="folio" class="form-label">Folio</label>
            <input type="text" id="folio" class="form-control" placeholder="Ingresa el folio">
        </div>

        <!-- Dropzone Form -->
        <form action="{{ route('upload.docs') }}" class="dropzone" id="file-dropzone" style="border: 2px dashed #007bff; padding: 20px;">
            @csrf
        </form>

        <!-- Botón para subir archivos -->
        <button id="upload-btn" class="btn btn-primary mt-3">Subir Archivo</button>
    </div>

    <!-- Script para Dropzone y funcionalidad del botón -->
    <script>
        Dropzone.options.fileDropzone = {
            autoProcessQueue: false, // No procesar automáticamente
            paramName: 'file', // Nombre del parámetro del archivo
            maxFilesize: 10, // Máximo 10 MB
            acceptedFiles: '.pdf,.doc,.docx,.txt', // Tipos permitidos
            dictDefaultMessage: 'Arrastra aquí tus archivos o haz clic para seleccionarlos',
            addRemoveLinks: true,
            maxFiles: 1, // Solo permitir un archivo
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function() {
                let dropzoneInstance = this;

                // Botón de carga
                document.getElementById('upload-btn').addEventListener('click', function() {
                    let folioInput = document.getElementById('folio').value;

                    // Validar que se ingresó un folio
                    if (!folioInput) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Por favor, ingresa un folio antes de subir el archivo.',
                            icon: 'error'
                        });
                        return;
                    }

                    // Agregar el folio como un campo adicional
                    dropzoneInstance.options.params = { folio: folioInput };

                    // Procesar la cola de Dropzone
                    dropzoneInstance.processQueue();
                });

                // Mensaje de éxito
                this.on("success", function(file, response) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Archivo subido correctamente',
                        icon: 'success'
                    });
                    dropzoneInstance.removeFile(file);
                });

                // Mensaje de error
                this.on("error", function(file, response) {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al subir el archivo',
                        icon: 'error'
                    });
                    dropzoneInstance.removeFile(file);
                });
            }
        };
    </script>
</body>
</html>
