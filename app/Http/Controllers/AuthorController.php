<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Tra cứu tác giả / đầu bếp
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $sort = $request->get('sort', 'popular');

        $query = User::whereHas('recipes', function ($query) {
            $query->where('status', 'published');
        })->withCount(['recipes' => function ($query) {
            $query->where('status', 'published');
        }]);

        // Tính cả rank (likes count), followers count nếu cần
        $query->withCount('followers');

        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'followers') {
            $query->orderByDesc('followers_count');
        } else {
            $query->orderByDesc('recipes_count');
        }

        $authors = $query->paginate(24)->withQueryString();

        return view('authors.index', compact('authors', 'q', 'sort'));
    }
}
