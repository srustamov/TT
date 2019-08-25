<?php

use App\Models\User;
use TT\Facades\Auth;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testAppIsBoot()
    {
        global $app;

        $this->assertTrue($app->isBoot());
    }


    public function testRouting()
    {
        global $app;

        $this->assertEquals(
            $app->routing(),$app->get('response')
        );
    }


    public function testAuth()
    {
        $isAuth = Auth::check();
        if ($isAuth) {
            $this->assertEquals($isAuth, true);
            if(Auth::guard() === 'default') {
                $this->assertEquals(
                    Auth::user() instanceof User
                );
            }
            
        } else {
            $this->assertEquals($isAuth, false);
        }
    }


   /*
    public function testUserModel()
    {
        $user = User::find(['name'=>'username']);

        if ($user) {
            $this->assertEquals($user->name, 'username');
        } else {
            $this->assertEquals($user, null);
        }
    }

    
    public function testModel()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->assertTrue(
                User::create([
                    'name' => microtime(),
                    'email' => microtime() . '@mail.com',
                    'password' => 'abc'
                ])
            );
            //$this->assertTrue(User::where('id', '>', 1)->delete());
        }

    }
    */
}
