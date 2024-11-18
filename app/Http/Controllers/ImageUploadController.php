<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'imageable_type' => 'required|string',
            'imageable_id' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $fileName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

            // Store image in public disk
            $path = $request->file('image')->storeAs('images', $fileName, 'public');

            $image = ImageUpload::create([
                'path' => $path,
                'imageable_type' => $fields['imageable_type'],
                'imageable_id' => $fields['imageable_id'],
            ]);

            // Return the full URL of the image
            $imageUrl = asset('storage/' . $path);

            return response()->json([
                'image' => [
                    'id' => $image->id,
                    'url' => $imageUrl,
                ]
            ], 201);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
