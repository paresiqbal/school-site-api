<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller implements HasMiddleware
{
    /**
     * Define middleware for the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        return Announcement::with(['user', 'tags', 'images'])->paginate(10);
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,name',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Create the announcement
        $announcement = $request->user()->announcements()->create([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imageFile->storePubliclyAs(
                    'announcements',
                    $imageFile->getClientOriginalName(),
                    'public'
                );
                $announcement->images()->create([
                    'path' => "announcements/{$imageFile->getClientOriginalName()}",
                ]);
            }
        }

        // Handle tags if provided
        if (!empty($fields['tags'])) {
            $tagIds = Tag::whereIn('name', $fields['tags'])->pluck('id');
            $announcement->tags()->attach($tagIds);
        }

        return response()->json([
            'message' => 'Announcement created successfully',
            'announcement' => $announcement->load(['user', 'tags', 'images']),
            'uploader_name' => $request->user()->name,
        ], 201);
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        return $announcement->load(['user', 'tags', 'images']);
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        Gate::authorize('modified', $announcement);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,name',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $announcement->update([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imageFile->storePubliclyAs(
                    'announcements',
                    $imageFile->getClientOriginalName(),
                    'public'
                );
                $announcement->images()->create([
                    'path' => "announcements/{$imageFile->getClientOriginalName()}",
                ]);
            }
        }

        return response()->json([
            'announcement' => $announcement->load(['user', 'images']),
            'uploader_name' => $announcement->user->name,
        ], 200);
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        Gate::authorize('modified', $announcement);

        foreach ($announcement->images as $image) {
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement and associated images deleted'], 200);
    }
}
