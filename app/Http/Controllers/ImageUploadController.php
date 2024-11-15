<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use App\Http\Requests\StoreImageUploadRequest;
use App\Http\Requests\UpdateImageUploadRequest;

class ImageUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageUploadRequest $request)
    {
        $fields = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageable_type' => 'required|string',
            'imageable_id' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $fileName = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('images', $fileName, 'public');

            $image = ImageUpload::create([
                'imageable_type' => $fields['imageable_type'],
                'imageable_id' => $fields['imageable_id'],
                'path' => $path
            ]);

            return response()->json(['image' => $image], 201);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageUploadRequest $request, ImageUpload $imageUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageUpload $imageUpload)
    {
        //
    }
}
