<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserPreferenceController extends Controller
{

    public function getPreferences(Request $request)
    { 
        $user = $request->user();
        
        $preferences = UserPreference::where('user_id', $user->id)->first();
        
        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return response()->json($preferences);
    }

    private function validate( $data ) 
    {
        $allowedSources = config('news.sources');

        return $data->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|string',
            'sources' => 'nullable|array',
            'sources.*' => [
                'string',
                Rule::in($allowedSources),
            ],
            'authors' => 'nullable|array',
            'authors.*' => 'nullable|string',
        ]);

        throw new \Exception("Saving preferences failed", 1);
    }

    public function savePreferences(Request $request)
    {
        try {
            // request already validated by SavePreferencesRequest
            $validated = $this->validate($request);

            $preference = UserPreference::updateOrCreate(
                ['user_id' => Auth::id()],
                $validated
            );
        
            return response()->json($preference, 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Saving preferences failed',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
}
