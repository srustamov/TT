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
                    'benchmark' => array($this, 'benchmark'),
                    'csrf' => array($this, 'csrf_field'),
                    'css' => array($this, 'css'),
                    'js' => array($this, 'js'),
            );
    }


    public function lang()
    {
        return "<?php echo lang".implode(',',func_get_args())."; ?>";
    }

    public function benchmark()
    {
        return "<?php echo benchmark_panel(); ?>";
    }

    public function csrf_field()
    {
      return '<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />';
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
