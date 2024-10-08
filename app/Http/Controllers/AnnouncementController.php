<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return Announcement::all();
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->move(public_path('announcement_images'), $request->file('image')->getClientOriginalName());
            $fields['image'] = 'announcement_images/' . $request->file('image')->getClientOriginalName();
        }

        $announcement = $request->user()->announcement()->create($fields);

        return response()->json([
            'message' => 'Announcement created successfully',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'image' => isset($fields['image']) ? asset($fields['image']) : null,
                'created_at' => $announcement->created_at,
                'updated_at' => $announcement->updated_at,
            ],
        ], 201);
    }

    public function show(Announcement $announcement)
    {
        return $announcement;
    }

    public function update(Request $request, Announcement $announcement)
    {
        Gate::authorize('modified', $announcement);


        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $announcement->update([
            'title' => $fields['title'],
            'content' => $fields['content'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcement_images', 'public');


            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }


            $announcement->update(['image' => $path]);
        }

        return response()->json($announcement, 200);
    }

    public function destroy(Announcement $announcement)
    {
        Gate::authorize('modified', $announcement);
        if ($announcement->image && file_exists(public_path($announcement->image))) {
            unlink(public_path($announcement->image));
        }

        $announcement->delete();

        return [
            'message' => 'Announcement deleted',
        ];
    }
}
