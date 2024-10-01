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

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->move(public_path('news_images'), $request->file('image')->getClientOriginalName());
            $fields['image'] = 'news_images/' . $request->file('image')->getClientOriginalName();
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

        // Validate the incoming request
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the image file
        ]);

        // Update title and content
        $news->update([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        // Handle file upload if an image is provided
        if ($request->hasFile('image')) {
            // Store the uploaded image
            $path = $request->file('image')->store('news_images', 'public'); // Store in 'public/news_images'

            // Optionally, delete the old image if needed
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            // Update the news record with the new image path
            $news->update(['image' => $path]);
        }

        return response()->json($news, 200); // Return updated news as JSON
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
