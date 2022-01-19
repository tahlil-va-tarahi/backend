<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategotyController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(20);

        return response()->json([
            'categories' => CategoryResource::collection($categories)
        ]);
    }
}
