<?php





class ExampleTest extends \PHPUnit_Framework_TestCase
{



      public function testAuth()
      {
        $auth = new \System\Libraries\Auth\Authentication();

        $this->assertTrue($auth->guest());

        $this->assertFalse($auth->check());

      }



      public function testUserModel()
      {
        $this->assertEquals((\App\Models\User::where('name', 'Samir')->first())->name,'Samir');
      }





}
