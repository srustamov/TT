<?php  namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
//use System\Engine\Http\Request;
use System\Facades\Response;
use System\Facades\Auth;
//use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Response::json(
            $this->transform(
                Auth::user()->articles()->limit(10)->get()
            )
        );
    }

    public function show($id)
    {
        $article = Auth::user()->article()->find( $id );

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
