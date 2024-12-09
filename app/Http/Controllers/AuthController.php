<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Método para registrar un usuario
    public function register(Request $request)
    {
        try {
            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed', // La contraseña debe confirmarse
                'tipo' => 'required|string',
            ]);

            // Asignar un rol al usuario (por ejemplo, 'user')
            $role = Role::findByName($request->tipo);
            if ( !$role ) {
                return response()->json(['message' => 'No existe el tipo de usuario'], 201);
            }

            // Si la validación falla, retorna un error
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Crear el usuario en la base de datos
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Encriptar la contraseña
            ]);

            $user->assignRole($role);

            return response()->json(['message' => 'Usuario registrado con exito'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }

    // Método para iniciar sesión y obtener un token
    public function login(Request $request)
    {
        // Validación de las credenciales
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Intentar autenticar al usuario
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('finanzas-archivos-api')->plainTextToken; // Crear el token

            return response()->json([
                'message' => 'Login exitoso',
                'token' => $token, // Regresar el token para futuras peticiones
            ]);
        } else {
            return response()->json(['message' => 'Credenciales invalidas'], 401);
        }
    }
}
