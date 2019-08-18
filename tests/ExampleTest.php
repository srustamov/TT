<?php



use App\Models\User;
use System\Facades\Auth;
use System\Engine\App;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testAppIsBoot()
    {
        $this->assertTrue(App::getInstance()->isBoot());
    }


    public function testAuth()
    {
        $isAuth = Auth::check();
        if ($isAuth) {
            $this->assertEquals($isAuth, true);
        } else {
            $this->assertEquals($isAuth, false);
        }
    }



    public function testUserModel()
    {
        $user = User::where('name', 'username')->first();

        if ($user) {
            $this->assertEquals($user->name, 'username');
        } else {
            $this->assertEquals($user, null);
        }
    }

    /*
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
