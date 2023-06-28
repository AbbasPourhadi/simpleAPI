<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);

    }


    /**
     * Store a newly created resource in storage.
     * @param StoreCategoryRequest $request
     * @return CategoryResource
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->all());
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findorfail($id);
        return new CategoryResource($category);
    }


    /**
     * Update the specified resource in storage.
     * @param StoreCategoryRequest $request
     * @param string $id
     */
    public function update(StoreCategoryRequest $request, string $id)
    {
        $category = Category::findorfail($id);
        $category->update($request->all());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findorfail($id);
        $category->delete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
