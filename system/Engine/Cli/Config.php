<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


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
    public static function clearConfigsCacheOrCreate($subCommand)
    {
        $file = App::instance()->configsCacheFile();

        if ($subCommand == '--create') {
            $configsArray = [];

            foreach (glob(path('app/Config/*.php')) as $file) {
                $configsArray[ substr(basename($file), 0, -4) ] = require $file;
            }

            file_put_contents($file, "<?php \n\n return array(\n\n");

            static::create($configsArray);

            file_put_contents($file, ");", FILE_APPEND);

            new PrintConsole('green', "\n\nConfigs cached successfully \n\n");
        } else {
            if (file_exists($file)) {
                unlink($file);
            }

            new PrintConsole('green', "\n\nCache configs clear successfully \n\n");
        }
    }

    protected static function create($configsArray)
    {

        $file = App::instance()->configsCacheFile();

        foreach ($configsArray as $key => $value) {
            if (is_array($value)) {
                file_put_contents($file,"\t'" . $key . "' => array(\n\n",FILE_APPEND);
                static::create($value);
                file_put_contents($file,"\t),\n\n",FILE_APPEND);
            } else {
                if (is_bool($value)) {
                    $value = $value ? "true" : "false";
                } elseif (\is_integer($value)) {
                } else {
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
