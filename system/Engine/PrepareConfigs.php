<?php namespace System\Engine;


class PrepareConfigs
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handle()
    {
        $configurations = [];

        $configs_cache_file = $this->app->configsCacheFile();

        if (file_exists($configs_cache_file)) {
            $configurations = require $configs_cache_file;
        } else {
            foreach (glob($this->app->configsPath('*')) as $file) {
                $configurations[pathinfo($file, PATHINFO_FILENAME)] = require $file;
            }
        }

        Load::register('config', new Config($configurations));
    }
}
