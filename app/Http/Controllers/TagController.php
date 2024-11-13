<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags|max:255',
        ]);

        $tag = Tag::create(['name' => $request->name]);

        return response()->json(['message' => 'Tag created', 'tag' => $tag], 201);
    }
}
