<?php



use App\Models\User;
use System\Facades\Auth;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testAuth()
    {
        $this->assertTrue(Auth::guest());

        $this->assertFalse(Auth::check());
    }



    public function testUserModel()
    {
        if ($user = User::where('name', 'username')->first()) {
            $this->assertEquals($user->name, 'username');
        }
    }
}
