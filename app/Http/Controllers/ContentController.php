<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $contents = Content::all();
        return response()->json($contents);
    }
    public function getByExploreId($explore_id)
    {
        $contents = Content::where('explore_id', $explore_id)->get();
        return response()->json($contents);
    }
    public function store(Request $request)
    {
        $content = new Content;
        $content->judul = $request->judul;
        $content->explore_id = $request->explore_id;
        $content->jenis = $request->jenis;

        $content->save();
        return response()->json($content, 201);
    }
}
