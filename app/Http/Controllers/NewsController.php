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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $news = $request->user()->news()->create([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        if ($request->hasFile('image')) {
            $imageController = new ImageUploadController();
            $imageController->store(new Request([
                'image' => $request->file('image'),
                'imageable_type' => News::class,
                'imageable_id' => $news->id,
            ]));
        }

        return response()->json([
            'message' => 'News created successfully',
            'news' => $news->load('images'),
            'uploader_name' => $request->user()->name,
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

        $news->update($fields);

        return response()->json([
            'news' => $news,
            'uploader_name' => $news->user->name,
        ], 200);
    }

    public function destroy(News $news)
    {
        Gate::authorize('modified', $news);

        foreach ($news->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }

        $news->delete();

        return response()->json(['message' => 'News and associated images deleted'], 200);
    }
}
