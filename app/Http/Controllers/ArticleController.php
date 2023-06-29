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
     * @OA\Get(
     *      path="/articles",
     *      tags={"Articles"},
     *      summary="Get list of articles",
     *      description="Returns list of articles",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        $articles = Article::with('photo', 'author', 'category')->get();
        return ArticleResource::collection($articles);
    }


    /**
     * @OA\Post(
     *      path="/articles",
     *      tags={"Articles"},
     *      summary="Store new article",
     *      description="Returns article data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
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
     * @OA\Get(
     *      path="/articles/{id}",
     *      tags={"Articles"},
     *      summary="Get article information",
     *      description="Returns article data",
     *      @OA\Parameter(
     *          name="id",
     *          description="article id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show(string $id)
    {
        $article = Article::with('author', 'photo', 'category')->findorfail($id);
        return new ArticleResource($article);
    }


    /**
     * @OA\Put(
     *      path="/articles/{id}",
     *      tags={"Articles"},
     *      summary="Update existing article",
     *      description="Returns updated article data",
     *      @OA\Parameter(
     *          name="category_id",
     *          description="article category id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          description="article title",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="photo",
     *          description="article photo",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="file"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="content",
     *          description="article content",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(StoreArticleRequest $request, string $id)
    {
        $article = Article::findorfail($id);
        $article->update($request->all());
        return new ArticleResource($article);
    }

    /**
     * @OA\Delete(
     *      path="/articles/{id}",
     *      tags={"Articles"},
     *      summary="Delete existing article",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="article id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy(string $id)
    {
        $article = Article::with('photo')->findorfail($id);
        if ($article->photo) {
            unlink($article->photo->url);
            $article->photo->delete();
        }
        $article->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
