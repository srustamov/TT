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

}
