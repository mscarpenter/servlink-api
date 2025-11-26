<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Shift;
use App\Models\ProfilesProfessional;
use App\Models\ProfilesEstablishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of ratings.
     * Returns ratings based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'professional') {
            // Professional sees ratings they received
            $ratings = Rating::with(['shift.job.establishment.user', 'giver'])
                ->where('receiver_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'establishment') {
            // Establishment sees ratings they received
            $ratings = Rating::with(['shift.job', 'shift.professional', 'giver'])
                ->where('receiver_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        return response()->json($ratings);
    }

    /**
     * Store a newly created rating (mutual rating system).
     * Both professional and establishment can rate each other after shift completion.
     */
    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'shift_id' => 'required|integer|exists:shifts,id',
            'score' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:500',
        ]);

        $shift = Shift::with(['job', 'application'])->findOrFail($validatedData['shift_id']);
        $giverId = Auth::id();

        // Determine receiver based on giver role
        if (Auth::user()->role === 'professional') {
            // Professional rates establishment
            if ($shift->professional_id !== $giverId) {
                return response()->json([
                    'message' => 'Você não participou deste turno.'
                ], 403);
            }
            $receiverId = $shift->job->establishment->user_id;
        } elseif (Auth::user()->role === 'establishment') {
            // Establishment rates professional
            $establishment = ProfilesEstablishment::where('user_id', $giverId)->first();
            if (!$establishment || $shift->job->establishment_id !== $establishment->id) {
                return response()->json([
                    'message' => 'Este turno não pertence ao seu estabelecimento.'
                ], 403);
            }
            $receiverId = $shift->professional_id;
        } else {
            return response()->json([
                'message' => 'Tipo de usuário inválido.'
            ], 400);
        }

        // Check if shift is completed
        if ($shift->status !== 'completed') {
            return response()->json([
                'message' => 'Apenas turnos concluídos podem ser avaliados.'
            ], 400);
        }

        // Check if user already rated this shift
        $existingRating = Rating::where('shift_id', $shift->id)
            ->where('giver_user_id', $giverId)
            ->first();

        if ($existingRating) {
            return response()->json([
                'message' => 'Você já avaliou este turno.',
                'rating' => $existingRating
            ], 409);
        }

        // Prevent self-rating
        if ($giverId === $receiverId) {
            return response()->json([
                'message' => 'Você não pode se auto-avaliar.'
            ], 400);
        }

        // Create rating
        $rating = Rating::create([
            'shift_id' => $shift->id,
            'giver_user_id' => $giverId,
            'receiver_user_id' => $receiverId,
            'score' => $validatedData['score'],
            'comments' => $validatedData['comments'] ?? null,
        ]);

        // Update receiver's overall rating
        $this->updateOverallRating($receiverId);

        $rating->load(['shift.job', 'giver', 'receiver']);

        return response()->json([
            'message' => 'Avaliação enviada com sucesso!',
            'rating' => $rating
        ], 201);
    }

    /**
     * Display the specified rating.
     */
    public function show(Rating $rating)
    {
        $user = Auth::user();

        // Check authorization - only giver or receiver can view
        if ($rating->giver_user_id !== $user->id && $rating->receiver_user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para visualizar esta avaliação.'
            ], 403);
        }

        $rating->load(['shift.job', 'giver', 'receiver']);

        return response()->json($rating);
    }

    /**
     * Update the overall rating for a user.
     * Calculates average of all ratings received.
     */
    private function updateOverallRating(int $userId)
    {
        // Calculate average rating
        $averageRating = Rating::where('receiver_user_id', $userId)
            ->avg('score');

        if ($averageRating === null) {
            return;
        }

        // Round to 2 decimal places
        $averageRating = round($averageRating, 2);

        // Update user's profile based on role
        $user = \App\Models\User::find($userId);
        
        if ($user->role === 'professional') {
            $profile = ProfilesProfessional::where('user_id', $userId)->first();
            if ($profile) {
                $profile->update(['overall_rating' => $averageRating]);
            }
        } elseif ($user->role === 'establishment') {
            $profile = ProfilesEstablishment::where('user_id', $userId)->first();
            if ($profile) {
                $profile->update(['average_rating' => $averageRating]);
            }
        }
    }

    /**
     * Get ratings for a specific user (public endpoint).
     */
    public function getUserRatings(Request $request, int $userId)
    {
        $ratings = Rating::with(['shift.job', 'giver'])
            ->where('receiver_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_ratings' => Rating::where('receiver_user_id', $userId)->count(),
            'average_rating' => round(Rating::where('receiver_user_id', $userId)->avg('score'), 2),
            'rating_distribution' => [
                '5_stars' => Rating::where('receiver_user_id', $userId)->where('score', 5)->count(),
                '4_stars' => Rating::where('receiver_user_id', $userId)->where('score', 4)->count(),
                '3_stars' => Rating::where('receiver_user_id', $userId)->where('score', 3)->count(),
                '2_stars' => Rating::where('receiver_user_id', $userId)->where('score', 2)->count(),
                '1_star' => Rating::where('receiver_user_id', $userId)->where('score', 1)->count(),
            ],
        ];

        return response()->json([
            'ratings' => $ratings,
            'stats' => $stats
        ]);
    }
}