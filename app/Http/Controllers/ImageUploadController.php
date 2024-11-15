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

            $path = $request->file('image')->storeAs('images', $fileName, 'public');

            $image = ImageUpload::create([
                'path' => $path,
                'imageable_type' => $fields['imageable_type'],
                'imageable_id' => $fields['imageable_id'],
            ]);

            return response()->json(['image' => $image], 201);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
