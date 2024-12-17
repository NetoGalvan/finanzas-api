<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    public function uploadDocs(Request $request)
    {
        // Valida el archivo
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'folio' => 'required|string|unique:files,folio',
        ], [
            'file.required' => 'Es necesario agregar un archivo.',
            'file.file' => 'El archivo debe ser un archivo válido.',
            'file.mimes' => 'El archivo debe ser de tipo: pdf, doc, docx o txt.',
            'file.max' => 'El archivo no debe superar los 10MB.',
            'folio.required' => 'El folio es necesario.',
            'folio.string' => 'El folio debe ser una cadena de texto.',
            'folio.unique' => 'El folio ya existe en nuestros registros.',
        ]);

        // Obtener el archivo del request
        $file = $request->file('file');

        // Definir el nombre del archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();

        // Guardar el archivo en el storage bajo la carpeta 'uploads'
        $path = $file->storeAs('archivos/'.$request->folio , $fileName, 'public');

        File::create([
            'user_id' => $request->user()->id,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'folio' => $request->folio,
            'tipo' => 'ORIGINAL',
        ]);

        // Retornar respuesta con información del archivo
        return response()->json([
            'message' => 'Archivo subido exitosamente',
            'path' => $path,
            'url' => Storage::url($path),
        ], 200);
    }

    public function setFile(Request $request)
    {
        // Valida el archivo
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'folio' => 'required|string',
        ], [
            'file.required' => 'Es necesario agregar un archivo.',
            'file.file' => 'El archivo debe ser un archivo válido.',
            'file.mimes' => 'El archivo debe ser de tipo: pdf, doc, docx o txt.',
            'file.max' => 'El archivo no debe superar los 10MB.',
            'folio.required' => 'El folio es necesario.',
            'folio.string' => 'El folio debe ser una cadena de texto.',
        ]);

        // Retornar respuesta con información del archivo
        return response()->json([
            'message' => 'Archivo recibido exitosamente',
        ], 200);
    }

    public function getFile($folio, $tipo = 'ORIGINAL')
    {
        // Buscar el archivo en la base de datos
        $file = File::where('folio', $folio)->where('tipo', $tipo)->first();

        if (!$file) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        // Obtener la ruta del archivo desde el sistema de almacenamiento
        $filePath = $file->file_path;
        $url = Storage::url($filePath);
        // dd($url, $filePath);

        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'El archivo no existe en el almacenamiento'], 404);
        }

        return Storage::disk('public')->download($filePath, $file->file_name);
    }

    public function uploadDocsExtra(Request $request)
    {
        // Valida el archivo
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'folio' => 'required|exists:files,folio',
        ], [
            'file.*' => 'Es necesario agregar un archivo.',
            'folio.exists' => 'El folio proporcionado no existe en nuestros registros.',
        ]);

        // Validar si ya existe un archivo procesado
        $fileRecordExists = File::where('folio', $request->folio)->where('tipo', 'PROCESADO')->exists();
        if ($fileRecordExists) {
            return response()->json(['error' => 'Ya existe un archivo procesado ligado al folio.'], 422);
        }

        // Recuperar el registro asociado al folio
        $fileRecord = File::where('folio', $request->folio)->first();
        // Verificar el tipo MIME del archivo
        $uploadedFileMimeType = $request->file('file')->getMimeType();
        if ($uploadedFileMimeType !== $fileRecord->file_type) {
            return response()->json(['error' => 'El tipo de archivo no coincide con el registrado para este folio.'], 422);
        }

        // Obtener el archivo del request
        $file = $request->file('file');

        // Definir el nombre del archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();

        // Guardar el archivo en el storage bajo la carpeta 'uploads'
        $path = $file->storeAs('archivos/'.$request->folio , $fileName, 'public');

        File::create([
            'user_id' => $request->user()->id,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'folio' => $request->folio,
            'tipo' => 'PROCESADO',
        ]);

        // Retornar respuesta con información del archivo
        return response()->json([
            'message' => 'Archivo subido exitosamente',
            'path' => $path,
            'url' => Storage::url($path),
        ], 200);
    }

    // WEB
    public function uploadDocsWeb(Request $request)
    {
        // Valida el archivo
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'folio' => 'required|string|unique:files,folio',
        ], [
            'file.required' => 'Es necesario agregar un archivo.',
            'file.file' => 'El archivo debe ser un archivo válido.',
            'file.mimes' => 'El archivo debe ser de tipo: pdf, doc, docx o txt.',
            'file.max' => 'El archivo no debe superar los 10MB.',
            'folio.required' => 'El folio es necesario.',
            'folio.string' => 'El folio debe ser una cadena de texto.',
            'folio.unique' => 'El folio ya existe en nuestros registros.',
        ]);

        // Obtener el archivo del request
        $file = $request->file('file');

        // Definir el nombre del archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();

        // Guardar el archivo en el storage bajo la carpeta 'uploads'
        $path = $file->storeAs('archivos/'.$request->folio , $fileName, 'public');

        File::create([
            'user_id' => Auth::user()->id,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'folio' => $request->folio,
            'tipo' => 'ORIGINAL',
        ]);

        // Retornar respuesta con información del archivo
        return response()->json([
            'message' => 'Archivo subido exitosamente',
            'path' => $path,
            'url' => Storage::url($path),
        ], 200);
    }
}
