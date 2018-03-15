<?php namespace App\Views\Edge;


use Windwalker\Edge\Extension\EdgeExtensionInterface;

class Extension implements EdgeExtensionInterface
{

    public function getName()
    {
        return 'my_extension';
    }

    public function getDirectives()
    {
        return array(
            'lang' => array($this, 'lang'),
            'csrf' => array($this, 'csrf'),
            'css' => array($this, 'css'),
            'js' => array($this, 'js'),
            'auth' => array($this, 'auth'),
            'endauth' => array($this, 'endauth'),
            'guest' => array($this, 'guest'),
            'endguest' => array($this, 'endguest'),
        );
    }


    public function lang()
    {
        return "<?php echo lang".implode(',',func_get_args())."; ?>";
    }


    public function csrf()
    {
        return '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />';
    }

    public function auth($guard = 'user')
    {
        return '<?php if(Auth::check('.$guard.')): ?>';
    }


    public function endauth()
    {
        return '<?php endif; ?>';
    }

    public function guest($guard = 'user')
    {
        return '<?php if(Auth::guest('.$guard.')): ?>';
    }


    public function endguest()
    {
        return '<?php endif; ?>';
    }

    public function css()
    {
        return '<?php echo css'.implode(',',func_get_args()).'; ?>';
    }

    public function js()
    {
        return '<?php echo js'.implode(',',func_get_args()).'; ?>';
    }

    public function img()
    {
        return '<?php echo img'.implode(',',func_get_args()).'; ?>';
    }


    public function getGlobals()
    {
        return array();
    }

    public function getParsers()
    {
        return array();
    }

}
