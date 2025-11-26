<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilesProfessional;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();

        if ($user->role === 'professional') {
            $profile = ProfilesProfessional::where('user_id', $user->id)->first();
        } else {
            $profile = ProfilesEstablishment::where('user_id', $user->id)->first();
        }

        return response()->json([
            'user' => $user,
            'profile' => $profile
        ]);
    }

    /**
     * Update professional profile.
     */
    public function updateProfessional(Request $request)
    {
        if (Auth::user()->role !== 'professional') {
            return response()->json([
                'message' => 'Apenas profissionais podem atualizar este perfil.'
            ], 403);
        }

        $validatedData = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'cpf' => ['sometimes', 'required', 'string', 'size:11', function ($attribute, $value, $fail) {
                if (!$this->validateCPF($value)) {
                    $fail('O CPF informado é inválido.');
                }
            }],
            'phone' => 'sometimes|nullable|string|max:20',
            'bio' => 'sometimes|nullable|string|max:500',
            'skills' => 'sometimes|nullable|array',
        ]);

        $profile = ProfilesProfessional::where('user_id', Auth::id())->first();

        if (!$profile) {
            return response()->json([
                'message' => 'Perfil não encontrado.'
            ], 404);
        }

        $profile->update($validatedData);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'profile' => $profile
        ]);
    }

    /**
     * Update establishment profile.
     */
    public function updateEstablishment(Request $request)
    {
        if (Auth::user()->role !== 'establishment') {
            return response()->json([
                'message' => 'Apenas estabelecimentos podem atualizar este perfil.'
            ], 403);
        }

        $validatedData = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'cnpj' => ['sometimes', 'required', 'string', 'size:14', function ($attribute, $value, $fail) {
                if (!$this->validateCNPJ($value)) {
                    $fail('O CNPJ informado é inválido.');
                }
            }],
            'address' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:500',
        ]);

        $profile = ProfilesEstablishment::where('user_id', Auth::id())->first();

        if (!$profile) {
            return response()->json([
                'message' => 'Perfil não encontrado.'
            ], 404);
        }

        $profile->update($validatedData);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'profile' => $profile
        ]);
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ]);

        $user = Auth::user();
        $file = $request->file('photo');
        
        // Generate unique filename
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store in public disk
        $path = $file->storeAs('profiles', $filename, 'public');

        // Update profile based on role
        if ($user->role === 'professional') {
            $profile = ProfilesProfessional::where('user_id', $user->id)->first();
            if ($profile) {
                // Delete old photo if exists
                if ($profile->photo_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $profile->photo_url));
                }
                $profile->update(['photo_url' => '/storage/' . $path]);
            }
        } else {
            $profile = ProfilesEstablishment::where('user_id', $user->id)->first();
            if ($profile) {
                // Delete old logo if exists
                if ($profile->logo_url) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $profile->logo_url));
                }
                $profile->update(['logo_url' => '/storage/' . $path]);
            }
        }

        return response()->json([
            'message' => 'Foto enviada com sucesso!',
            'url' => '/storage/' . $path
        ]);
    }

    /**
     * Upload documents (for professionals).
     */
    public function uploadDocument(Request $request)
    {
        if (Auth::user()->role !== 'professional') {
            return response()->json([
                'message' => 'Apenas profissionais podem enviar documentos.'
            ], 403);
        }

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'type' => ['required', Rule::in(['certificate', 'id', 'other'])],
        ]);

        $user = Auth::user();
        $file = $request->file('document');
        $type = $request->type;
        
        // Generate unique filename
        $filename = 'doc_' . $user->id . '_' . $type . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store in private disk (not publicly accessible)
        $path = $file->storeAs('documents', $filename, 'local');

        return response()->json([
            'message' => 'Documento enviado com sucesso!',
            'filename' => $filename,
            'type' => $type
        ]);
    }

    /**
     * Validate CPF (Brazilian individual taxpayer registry).
     */
    private function validateCPF(string $cpf): bool
    {
        // Remove non-numeric characters
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Check if it has 11 digits
        if (strlen($cpf) != 11) {
            return false;
        }

        // Check for known invalid CPFs
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Validate first digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if (intval($cpf[9]) != $digit1) {
            return false;
        }

        // Validate second digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return intval($cpf[10]) == $digit2;
    }

    /**
     * Validate CNPJ (Brazilian company taxpayer registry).
     */
    private function validateCNPJ(string $cnpj): bool
    {
        // Remove non-numeric characters
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Check if it has 14 digits
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Check for known invalid CNPJs
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validate first digit
        $sum = 0;
        $multipliers = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += intval($cnpj[$i]) * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if (intval($cnpj[12]) != $digit1) {
            return false;
        }

        // Validate second digit
        $sum = 0;
        $multipliers = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += intval($cnpj[$i]) * $multipliers[$i];
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return intval($cnpj[13]) == $digit2;
    }
    /**
     * List all professionals.
     */
    public function indexProfessionals()
    {
        $professionals = User::where('role', 'professional')
            ->with('professionalProfile')
            ->get();
            
        return response()->json($professionals);
    }

    /**
     * Show a specific professional profile.
     */
    public function showProfessional($id)
    {
        $user = User::where('id', $id)->where('role', 'professional')->with('professionalProfile')->firstOrFail();
        return response()->json($user);
    }
}
