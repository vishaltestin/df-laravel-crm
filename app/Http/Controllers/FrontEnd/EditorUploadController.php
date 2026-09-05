<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);

        $path = $request->file('file')->store('tinymce/images', 'public');

        return response()->json([
            'location' => asset('storage/' . $path)
        ]);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120',
        ]);

        $path = $request->file('file')->store('tinymce/files', 'public');

        return response()->json([
            'location' => asset('storage/' . $path)
        ]);
    }
}
