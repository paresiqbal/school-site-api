<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Tag;
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
        // Validate the incoming request
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,name',
        ]);

        // Handle the image upload if an image file is provided
        if ($request->hasFile('image')) {
            $fileName = 'news_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('news_images', $fileName, 'public');
            $fields['image'] = $path;
        } else {
            $fields['image'] = null;
        }

        // Create the news entry in the database, associating it with the current user
        $news = $request->user()->news()->create([
            'title' => $fields['title'],
            'content' => $fields['content'],
            'image' => $fields['image'],
        ]);


        if (!empty($fields['tags'])) {
            $tagIds = Tag::whereIn('name', $fields['tags'])->pluck('id');
            $news->tags()->attach($tagIds);
        }

        return response()->json([
            'message' => 'News created successfully',
            'news' => $news->load('tags'),
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
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,name',
        ]);

        $news->update($fields);

        if (!empty($fields['tags'])) {
            $tagIds = Tag::whereIn('name', $fields['tags'])->pluck('id');
            $news->tags()->sync($tagIds);
        }

        return response()->json([
            'news' => $news->load('tags'),
            'uploader_name' => $news->user->name,
        ], 200);
    }

    public function destroy(News $news)
    {
        Gate::authorize('modified', $news);

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return response()->json(['message' => 'News and associated image deleted'], 200);
    }
}
