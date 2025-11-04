<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * (Cria a avaliação mútua)
     */
    public function store(Request $request)
    {
        // Validação: Precisamos saber quem está avaliando quem, sobre qual turno, e a nota
        $validatedData = $request->validate([
            'shift_id' => 'required|integer|exists:shifts,id',
            'giver_user_id' => 'required|integer|exists:users,id',
            'receiver_user_id' => 'required|integer|exists:users,id',
            'score' => 'required|integer|min:1|max:5', // Nota de 1 a 5
            'comments' => 'nullable|string',
        ]);

        $rating = Rating::create($validatedData);

        // (No futuro, esta ação atualizaria a nota média do 'receiver_user_id')

        return response()->json($rating, 201);
    }

    // (O resto das funções (index, show, etc.) ficam vazias por enquanto)
    public function index() { return Rating::all(); }
    public function show(Rating $rating) { return $rating; }
    public function update(Request $request, Rating $rating) { /* ... */ }
    public function destroy(Rating $rating) { /* ... */ }
}