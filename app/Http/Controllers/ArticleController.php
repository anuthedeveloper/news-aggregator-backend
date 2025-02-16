<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchArticleRequest;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();
        
         // Apply user preferences if authenticated
        if (Auth::check()) {
            $preferences = Cache::remember('user_preferences_' . Auth::id(), now()->addMinutes(30), function() {
                return UserPreference::where('user_id', Auth::id())->first();
            });

            if ($preferences && ($preferences->sources === null || $preferences->categories === null)) {
                return response()->json([
                    'message' => 'Preferences are incomplete. Please set your preferences in the profile.'
                ], 400);
            }
            
            if ($preferences) {
                $query->filterByPreferences($preferences);
            }
        }
        
        $articles = $query->latest()->paginate(10);

        return response()->json($articles);
    }
    
    public function searchArticles(SearchArticleRequest $request)
    {
        $query = Article::query();

        // Apply filters based on user request
        if ($request->filled('keyword')) {
            $query->searchByKeyword($request->keyword);
        }

        if ($request->filled('category')) {
            $query->filterByCategory($request->category);
        }

        if ($request->filled('source')) {
            $query->filterBySource($request->source);
        }

        if ($request->filled('date')) {
            $query->filterByDate($request->date);
        }

        // Apply pagination
        $articles = $query->orderBy('published_at', 'desc')->paginate(10);
        // $pagination = [
        //     'current_page' => $articles->currentPage(),
        //     'last_page' => $articles->lastPage(),
        //     'per_page' => $articles->perPage(),
        //     'total' => $articles->total(),
        // ];
        // $response_data = [
        //     'data' => $articles->items(),
        //     'pagination' => $pagination,
        // ];
        return response()->json($articles);
    }

}
