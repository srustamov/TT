<?php  namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use App\Models\Article;
use TT\Facades\Response;
use TT\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Response::json(
            $this->transform(
                Auth::user()->articles
            )
        );
    }

    public function show($id)
    {
        $article = Article::find([
            'user_id' => Auth::user()->id,
            'id' => $id
        ]);

        if($article) {
            return Response::json($this->transform($article));
        }

        return Response::json(['success' => false],404);

    }


    /**
     * @param object|array $data
     * @return array
     */
    public function transform($data): array
    {
        $articles = is_array($data) ? $data : [$data];

        $transform = [];

        $author = Auth::user()->name;

        $transform['success'] = true;

        foreach ($articles as $article) {
            $transform['data'][] = [
              'id' => $article->id,
              'author' => $author
          ];
        }

        $transform['version'] = '1.3.0';

        return $transform;
    }
}
