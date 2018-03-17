<?php

/**
* @author  Samir Rustamov <rustemovv96@gmail.com>
* @link    https://github.com/srustamov/TT
*/

namespace System\Engine\Cli;

/**
 * Description of CreateTables
 *
 * @author Samir Rustamov
 */

use System\Engine\Cli\PrintConsole;
use System\Facades\Load;
use System\Facades\DB;

class CreateTables {


    public static function session($manage)
    {
        $table = false;

        if (isset( $manage[ 1 ] ) && $manage[ 1 ] == '--create')
        {
            if (isset( $manage[ 2 ] ) && !empty( $manage[ 2 ] ))
            {
                $table = $manage[ 2 ];
            }
        }
        if (!$table)
        {
            $table = Load::config ( 'session.table' , 'sessions' );
        }

        try
        {
            DB::exec (static::getSessionTableSql($table));

            new PrintConsole ( 'success' , "\n Create session table successfully \n\n" );

        }
        catch (\PDOException $e)
        {
            if ($e->getCode () == "42S01")
            {
                new PrintConsole ( 'error' , "\n\n {$table} table or view already exists\n" );
            }
            else
            {
                new PrintConsole ( 'error' , "\n\n {$e->getmessage()}\n" );
            }

            new PrintConsole ( 'red' , "\n \n" );
        }

    }



    public static function users()
    {

        try
        {
            DB::exec ( static::getUsersTableSql() );
            
            new PrintConsole ( 'green' , "\nUsers table created successfully\n\n" );
        }
        catch (\PDOException $e)
        {
            if ($e->getCode () == "42S01")
            {
                new PrintConsole ( 'error' , "\n\n users table or view already exists\n" );
            }
            else
            {
                new PrintConsole ( 'error' , "\n\n {$e->getmessage()}\n" );
            }
        }
    }



    private static function getSessionTableSql($table)
    {
        return  "CREATE TABLE IF NOT EXISTS {$table} (
                      session_id varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      expires int(100) NOT NULL,
                      data text COLLATE utf8_unicode_ci,
                       PRIMARY KEY(session_id)
                     )";
    }


    private static function getUsersTableSql()
    {
        return "CREATE TABLE IF NOT EXISTS users (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    password varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    status tinyint(1) NOT NULL DEFAULT '1',
                    remember_token varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                    created_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    forgotten_pass_code varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                    PRIMARY KEY (id))";
    }
}
