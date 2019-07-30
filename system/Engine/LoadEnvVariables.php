<?php namespace System\Engine;


class LoadEnvVariables
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handle()
    {
        $this->prepareEnv();
    }

    private function setEnv($data)
    {
        foreach ($data as $key => $value) {
            $_ENV[ $key ] = $value;
        }
    }

    private function isModified()
    {
        $cacheFile = $this->app->envCacheFile();

        $modified =  (!file_exists($cacheFile) ||
                filemtime($cacheFile) < filemtime($this->app->envFile()));

        if (!$modified) {
            $data =  unserialize(file_get_contents($cacheFile));

            $this->setEnv($data);
        }

        return $modified;
    }

    private function lines()
    {
        $autoDetect = ini_get('auto_detect_line_endings');

        ini_set('auto_detect_line_endings', 1);

        $lines = file($this->app->envFile(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        ini_set('auto_detect_line_endings', $autoDetect);

        return $lines;
    }

    private function isComment($line)
    {
        return (isset($line[ 0 ]) && $line[ 0 ] === '#');
    }

    private function getBoolValueOrValue($value)
    {
        if (strtolower($value) == 'true') {
            $value = true;
        } elseif (strtolower($value) == 'false') {
            $value = false;
        }

        return $value;
    }

    public function prepareEnv()
    {
        if ($this->isModified()) {
            $settings = [];

            foreach ($this->lines() as $line) {
                $line = trim($line);

                if ($this->isComment($line)) {
                    continue;
                }

                if (strpos($line, '=') !== false) {
                    list($name, $value) = array_map('trim', explode('=', $line, 2));

                    $name = str_replace(['\'','"'], '', $name);

                    if (preg_match('/\s+/', $value) > 0) {
                        throw new \RuntimeException("setting variable value containing spaces must be surrounded by quotes");
                    }

                    $value = $this->getBoolValueOrValue($value);

                    $settings[ $name ] = $value;
                }
            }


            foreach ($settings as $key => $value) {
                if (strpos($value, '$') !== false) {
                    $settings[ $key ] = preg_replace_callback(
                        '/\${([a-zA-Z0-9_]+)}/',
                        function ($m) use ($settings) {
                            if (isset($settings[ $m[ 1 ] ])) {
                                return $settings[ $m[ 1 ] ];
                            } else {
                                return ${"$m[1]"} ?? '${' . $m[ 1 ] . '}';
                            }
                        },
                        $value
                    );
                }
            }

            $this->setEnv($settings);

            file_put_contents($this->app->envCacheFile(), serialize($settings));
        }
    }
}
