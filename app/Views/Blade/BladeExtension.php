<?php namespace App\Views\Blade;


use Windwalker\Edge\Extension\EdgeExtensionInterface;

class BladeExtension implements EdgeExtensionInterface
{

  public function getName()
	{
		return 'my_extension';
	}

	public function getDirectives()
	{
		return array(
			'lang' => array($this, 'lang'),
			'benchmark_panel' => array($this, 'benchmark_panel'),
		);
	}

	public function getGlobals()
	{
		return array();
	}

	public function getParsers()
	{
		return array();
	}

	public function lang()
	{
		return "<?php echo lang".implode(',',func_get_args())."; ?>";
	}

	public function benchmark_panel()
	{
		return "<?php echo benchmark_panel(); ?>";
	}

}
