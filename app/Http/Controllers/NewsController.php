<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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

        // Store the image in the public disk and get the path
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news_images', 'public');
            $fields['image'] = $path;
        }

        $news = $request->user()->news()->create($fields);

        return response()->json([
            'message' => 'News created successfully',
            'news' => [
                'id' => $news->id,
                'title' => $news->title,
                'content' => $news->content,
                'image' => isset($fields['image']) ? asset($fields['image']) : null,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $news->update([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news_images', 'public');


            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }


            $news->update(['image' => $path]);
        }

        return response()->json($news, 200);
    }

    public function destroy(News $news)
    {
        Gate::authorize('modified', $news);
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }

        $news->delete();

        return [
            'message' => 'News deleted',
        ];
    }
}
