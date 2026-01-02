<?php

namespace App\Http\Controllers;

use App\Models\hashtags;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    public function popular()
    {
        $hashtags = hashtags::orderBy('usage_count', 'desc')
            ->take(5)
            ->pluck('name');

        return response()->json([
            'hashtags' => $hashtags,
        ]);
    }

    public function index(Request $request)
    {
        $query = hashtags::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $hashtags = $query->orderBy('usage_count', 'desc')
            ->paginate(20);

        return response()->json($hashtags);
    }
}