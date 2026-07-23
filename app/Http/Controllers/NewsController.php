<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = NewsItem::query()
            ->when($request->query('sentiment'), fn ($q, $s) => $q->where('sentiment', $s))
            ->when($request->query('impact'), fn ($q, $i) => $q->where('impact', $i))
            ->orderByDesc('published_at')
            ->paginate(15)
            ->withQueryString();

        return view('news.index', compact('news'));
    }
}
