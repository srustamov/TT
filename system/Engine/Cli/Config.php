<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


namespace System\Engine\Cli;

/**
 * Description of Config
 *
 * @author Samir Rustamov
 */

use System\Engine\App;

class Config
{
    /**
     * @param $subCommand
     */
    public static function clearConfigsCacheOrCreate($subCommand)
    {
        $file = App::getInstance()->configsCacheFile();

        if ($subCommand === '--create') {
            $configsArray = [];

            foreach (glob(path('app/Config/*.php')) as $config) {
                $configsArray[ substr(basename($config), 0, -4) ] = require $config;
            }

            file_put_contents($file, "<?php \n\n return array(\n\n");

            static::create($configsArray);

            file_put_contents($file, ');', FILE_APPEND);

            new PrintConsole('green', "\n\nConfigs cached successfully \n\n");
        } else {
            if (file_exists($file)) {
                unlink($file);
            }

            new PrintConsole('green', "\n\nCache configs clear successfully \n\n");
        }
    }

    /**
     * @param $configsArray
     */
    protected static function create($configsArray)
    {
        $file = App::getInstance()->configsCacheFile();

        foreach ($configsArray as $key => $value) {
            if (is_array($value)) {
                file_put_contents($file, "\t'" . $key . "' => array(\n\n", FILE_APPEND);
                static::create($value);
                file_put_contents($file, "\t),\n\n", FILE_APPEND);
            } else {
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (!\is_int($value)) {
                    $value = "'$value'";
                }

                if (is_numeric($key)) {
                    file_put_contents($file, "\t$value, \n\n", FILE_APPEND);
                } else {
                    file_put_contents($file, "\t'" . $key . "' => $value, \n\n", FILE_APPEND);
                }
            }
        }
    }
}
