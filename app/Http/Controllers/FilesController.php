<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);
        $file = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->extension();
        $file->storeAs('images', $fileName, 'public');
        return response()->json($fileName);
    }
}
