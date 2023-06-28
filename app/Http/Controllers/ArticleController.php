<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Photo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with('photo','author','category')->get();
        return  ArticleResource::collection($articles);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     * @param StoreArticleRequest $request
     */
    public function store(StoreArticleRequest $request)
    {
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = uniqid() . '.' . $file->extension();
            $path = $file->storeAs('images/articles', $name);
            $photo = Photo::create([
                'name' => $name,
                'url' => $path
            ]);
            $data['photo_id'] = $photo->id;
        }
        $article = Article::create($data);

        return new ArticleResource($article);
    }

    /**
     * Display the specified resource.
     * @param string $id
     */
    public function show(string $id)
    {
        $article = Article::with('author','photo','category')->findorfail($id);
        return new ArticleResource($article);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     * @param StoreArticleRequest $request
     * @param string $id
     */
    public function update(StoreArticleRequest $request, string $id)
    {
        $article = Article::findorfail($id);
        $article->update($request->all());
        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     */
    public function destroy(string $id)
    {
        $article = Article::with('photo')->findorfail($id);
        if ($article->photo){
            unlink($article->photo->url);
            $article->photo->delete();
        }
        $article->delete();
        return response(null,Response::HTTP_NO_CONTENT);

    }
}
