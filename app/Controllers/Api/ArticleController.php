<?php

namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use App\Models\Article;
use TT\Facades\Response;

class ArticleController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $user = auth()->user();

        return Response::json(
            $this->transform(
                $user->articles ?? [] , $user
            )
        );
    }

    public function show($id)
    {
        $user = auth()->user();

        $article = Article::find([
            'user_id' => $user->id,
            'id' => $id
        ]);

        if ($article) {
            return Response::json($this->transform($article,$user, true));
        }

        return Response::json(['success' => false], 404);
    }


    /**
     * @param object|array $data
     * @return array
     */
    public function transform($data, $user, $one = false): array
    {
        $transform = [];

        $author = $user->name;

        $transform['success'] = true;

        if (!$one) {
            $transform['data'] = array_map(function ($article) use ($author) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'published date' => $article->created_at,
                    'author' => $author,
                ];
            }, $data);
        } else {
            $transform['data'] = [
                'id' => $data->id,
                'title' => $data->title,
                'content' => $data->content,
                'published date' => $data->created_at,
                'author' => $author,
            ];
        }



        $transform['version'] = '1.3.0';

        return $transform;
    }
}
