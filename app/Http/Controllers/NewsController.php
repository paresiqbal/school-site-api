<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class NewsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return News::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news_images', 'public');
            $fields['image'] = asset('storage/' . $imagePath);
        }

        $news = $request->user()->news()->create($fields);

        return response()->json([
            'message' => 'News created successfully',
            'news' => [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'image' => isset($fields['image']) ? $fields['image'] : null,
                'created_at' => $news->created_at,
                'updated_at' => $news->updated_at,
            ],
        ], 201);
    }

    public function show(News $news)
    {
        return $news;
    }

    public function update(Request $request, News $news)
    {
        Gate::authorize('modified', $news);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $news->update($fields);

        return $news;
    }

    public function destroy(News $news)
    {
        Gate::authorize('modified', $news);

        $news->delete();

        return [
            'message' => 'News deleted',
        ];
    }
}
