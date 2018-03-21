<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Hash
 */


use RuntimeException;

class Hash
{
    protected $round = 10;



    public function make($value, $option = []):String
    {
        $hash =  password_hash($value, PASSWORD_BCRYPT, ['cost' => $this->cons($option)]);

        if ($hash === false)
        {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }



    public function check($value, $hash):Bool
    {
        if (strlen($hash) == 0) {
            return false;
        }
        return password_verify($value, $hash);
    }




    public function round($round):Hash
    {
        $this->round = $round;
        return $this;
    }



    protected function cons($option):Int
    {
        return $option['round'] ?? $this->round;
    }


}
