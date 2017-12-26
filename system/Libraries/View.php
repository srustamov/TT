<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage  Libraries
 * @category   View
 */



use Windwalker\Edge\Cache\EdgeFileCache;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Loader\EdgeFileLoader;


class View
{
    protected $no_cache_replacment = [
        // '[['          => '<?php echo htmlspecialchars(',
        // ']]'          => ',ENT_QUOTES);? >',
        // '[!!'         => '< ? php echo ',
        // '!!]'         => ';? >',
        '@nocache'    => '<?php ',
        '@endnocache' => ' ?>'
    ];


    protected $html_cache_path    = 'storage/cache/html/';

    protected $file;

    protected $data = [];

    protected $cache = true;

    protected $htmlCache = false;


    public function render(String $file, $_data = [], $cache = true)
    {
        $this->file  = $file;
        $this->data  = array_merge($this->data, $_data);
        $this->cache = $cache;
        return $this;
    }


    public function data($key, $value = null)
    {
        if (is_array($key))
        {
            $this->data = array_merge($this->data, $key);
        }
        else
        {
            $this->data[ $key ] = $value;
        }

        return $this;
    }


    public function htmlCache()
    {
        $this->htmlCache  = true;
    }


    private function renderHtmlCache()
    {
        $auth = app('auth')->check() ? 'auth' : 'guest';
        $file = $this->html_cache_path. md5($this->file .app('lang')->locale(). $auth).'.php';
        if (file_exists($file))
        {
          if(filemtime($file) >= filemtime(APPDIR.'Views/'.str_replace('.','/',$this->file).'.blade.php'))
            return $file;
        }
        return false;
    }


    private function writeHtmlCache($content)
    {
        $content = str_replace(
                         array_keys($this->no_cache_replacment),
                         array_values($this->no_cache_replacment),
                         $content
                       );

        $auth       = app('auth')->check() ? 'auth' : 'guest';

        $cache_file = $this->html_cache_path. md5($this->file.app('lang')->locale().$auth).'.php';

        if (!is_dir(dirname($cache_file)))
    		{
    			mkdir(dirname($cache_file), 0755, true);
    		}

        return file_put_contents($cache_file, $content);

    }


    private function htmlCacheNormalize()
    {

    }


    public function __destruct()
    {
        if (app('session')->has(md5('redirectWithData')))
        {
            $redirect_variable_name = app('session')->get(md5('redirectWithVariableName'));
            $redirect_data          = app('session')->get(md5('redirectWithData'));
            if (!isset($this->data[ $redirect_variable_name ]))
            {
                $this->data[ $redirect_variable_name ] = $redirect_data;
                app('session')->delete([md5('redirectWithData'),md5('redirectWithVariableName')]);
            }
        }

        if ($this->htmlCache)
        {
            if ($file = $this->renderHtmlCache())
            {
                extract($this->data);
                return require_once $file;
            }
        }


        $loader = new EdgeFileLoader(array( APPDIR.'Views' ));

        $loader->addFileExtension('.php');

        if ($this->cache == false)
        {
            $edge = new Edge($loader);
        }
        else
        {
            $edge = new Edge($loader, null, new EdgeFileCache(BASEDIR.'/storage/cache/views'));
        }

        $compiler = $edge->getCompiler();

        if($extension = config('blade.extension'))
        {
          $extension = '\\'.trim($extension,'\\');
          
          $edge->addExtension(new $extension);
        }




        if(!$this->htmlCache)
        {
            $compiler = $edge->getCompiler();

            $compiler->directive('nocache',function(){
                return '<?php ';
            });
            $compiler->directive('endnocache',function(){
                return ' ?>';
            });

        }

        $content = $edge->render($this->file, $this->data);

        if ($this->htmlCache)
        {
            if ($this->writeHtmlCache($content))
            {
                if ($file = $this->renderHtmlCache())
                {
                    extract($this->data);
                    return require_once $file;
                }
            }
        }
        echo $content;
    }




}
