<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::orderBy('order')->get();
        return response()->json($categories);
    }
}
