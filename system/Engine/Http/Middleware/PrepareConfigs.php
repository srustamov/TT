<?php


namespace System\Engine\Http\Middleware;

use System\Engine\Kernel;
use System\Engine\Config;
use System\Engine\Load;

class PrepareConfigs
{

    public function handle()
    {
        $configurations = [];

        $configs_path       = rtrim(Kernel::instance()->configs_path(),DIRECTORY_SEPARATOR);

        $configs_cache_file = Kernel::instance()->configs_cache_file();

        if(file_exists($configs_cache_file))
        {
            $configurations = require_once $configs_cache_file;
        }
        else
        {
            foreach (glob(path($configs_path.DIRECTORY_SEPARATOR.'*')) as $file) {
                $configurations[pathinfo($file ,PATHINFO_FILENAME)] = require_once $file;
            }
        }

        Load::register('config',new Config($configurations));

    }

}