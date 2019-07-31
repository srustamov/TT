<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static size(String $path)
 * @method static get(String $path)
 * @method static prepend(string $fixPath, $content)
 * @method static append(string $fixPath, $content)
 * @method static deleteDirectory(string $fixPath)
 * @method static delete(string $fixPath)
 * @method static copy(string $fixPath, string $fixPath1)
 * @method static move(string $fixPath, string $fixPath1)
 * @method static setDir(string $fixPath, int $mode)
 * @method static create(string $fixPath)
 * @method static lastModifiedTime(string $fixPath)
 */



class File extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'file';
    }
}
