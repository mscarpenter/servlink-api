<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilesEstablishment;
use App\Models\ProfilesProfessional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     * TAREFA 1 DO SPRINT
     */
    public function register(Request $request)
    {
        // 1. Validação dos dados que chegam
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:professional,establishment',
            
            // Campos específicos do perfil (opcionais no registro inicial)
            'company_name' => 'required_if:role,establishment|string|max:255',
            'full_name' => 'required_if:role,professional|string|max:255',
        ]);

        // 2. Criptografa a senha (Segurança)
        $hashedPassword = Hash::make($validatedData['password']);

        // 3. Cria o Usuário (a conta de login)
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $hashedPassword,
            'role' => $validatedData['role'],
        ]);

        // 4. Cria o Perfil correspondente (Lógica de Negócio)
        if ($validatedData['role'] == 'professional') {
            ProfilesProfessional::create([
                'user_id' => $user->id,
                'full_name' => $validatedData['full_name'],
            ]);
        } elseif ($validatedData['role'] == 'establishment') {
            ProfilesEstablishment::create([
                'user_id' => $user->id,
                'company_name' => $validatedData['company_name'],
            ]);
        }

        // 5. Cria um Token de API (Login automático)
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Retorna o usuário criado e o token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201); // 201 = Created
    }

    /**
     * Handle user login.
     * TAREFA 2 DO SPRINT
     */
    public function login(Request $request)
    {
        // 1. Validação
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Busca o usuário
        $user = User::where('email', $credentials['email'])->first();

        // 3. Verifica o usuário e a senha
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401); // 401 = Unauthorized
        }

        // 4. Cria e retorna o Token de API
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Handle user logout.
     * Revoke the current access token.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ]);
    }
}