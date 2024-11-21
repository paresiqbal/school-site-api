<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class AchievementController extends Controller implements HasMiddleware
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
     * Display a listing of achievements.
     */
    public function index()
    {
        return Achievement::with(['user', 'images'])->get();
    }

    /**
     * Store a newly created achievement in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // Create the achievement
        $achievement = $request->user()->achievements()->create([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // Create a new Request instance for each image
                $imageRequest = new Request([
                    'image' => $imageFile,
                    'imageable_type' => Achievement::class,
                    'imageable_id' => $achievement->id,
                ]);

                // Use the ImageUploadController to store the image
                $imageController = new ImageUploadController();
                $imageController->store($imageRequest);
            }
        }

        return response()->json([
            'message' => 'Achievement created successfully',
            'achievement' => $achievement->load(['user', 'images']),
            'uploader_name' => $request->user()->name,
        ], 201);
    }

    /**
     * Display the specified achievement.
     */
    public function show(Achievement $achievement)
    {
        return $achievement->load(['user', 'images']);
    }

    /**
     * Update the specified achievement in storage.
     */
    public function update(Request $request, Achievement $achievement)
    {
        Gate::authorize('modified', $achievement);

        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // Update title and content
        $achievement->update([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        // Handle new image uploads if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // Create a new Request instance for each image
                $imageRequest = new Request([
                    'image' => $imageFile,
                    'imageable_type' => Achievement::class,
                    'imageable_id' => $achievement->id,
                ]);

                // Use the ImageUploadController to store the image
                $imageController = new ImageUploadController();
                $imageController->store($imageRequest);
            }
        }

        return response()->json([
            'achievement' => $achievement->load(['user', 'images']),
            'uploader_name' => $achievement->user->name,
        ], 200);
    }

    /**
     * Remove the specified achievement from storage.
     */
    public function destroy(Achievement $achievement)
    {
        Gate::authorize('modified', $achievement);

        // The images are deleted via the Achievement model's boot method

        $achievement->delete();

        return response()->json(['message' => 'Achievement and associated images deleted'], 200);
    }
}
