<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que la app mande los datos requeridos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar al usuario en la base de datos PostgreSQL
        $user = User::where('email', $request->email)->first();

        // 3. Verificar si el usuario existe y si la contraseña coincide
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales introducidas son incorrectas.'
            ], 401); // 401 significa No Autorizado
        }

        // 4. Si todo está bien, creamos un token de acceso para el teléfono móvil
        // Puedes ponerle el nombre que quieras, ej: 'phone_app'
        $token = $user->createToken('auth_token_movil')->plainTextToken;

        // 5. Devolvemos el token a React Native y los datos del usuario
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    // Método opcional para cuando quieras cerrar sesión desde la app
    public function logout(Request $request)
    {
        // Borra el token actual que está usando el teléfono
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente en el dispositivo.'
        ]);
    }

    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first()
        ], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('valkabit_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'access_token' => $token,
        'user' => $user
    ], 201);
}
}
