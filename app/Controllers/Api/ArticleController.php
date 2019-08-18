<?php  namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Response;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return Response::json(
            $this->transform(
                Article::where(['user_id' => auth()->user()->id])->get()
            )
        );
    }

    public function show($id)
    {
        $article = Article::where([
                                'id' => $id,
                                'user_id' => auth()->user()->id,
                            ])->get();
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

        $author = auth()->user()->name;

        $transform['success'] = true;

        foreach ($articles as $article) {
            $transform['data'][] = [
              'title' => $article->title,
              'body' => $article->body,
              'author' => $author
          ];
        }

        $transform['version'] = '1.3.0';

        return $transform;
    }
}
