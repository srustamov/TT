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

        $cache_file = (string)$this->app->configsCacheFile();

        if (file_exists($cache_file)) {
            $configurations = require $cache_file;
        } else {
            foreach (glob($this->app->configsPath('*')) as $file) {
                $configurations[pathinfo($file, PATHINFO_FILENAME)] = require $file;
            }
        }

        $this->app::register('config', new Config($configurations));
    }
}
