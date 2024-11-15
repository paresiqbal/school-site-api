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
        //
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
