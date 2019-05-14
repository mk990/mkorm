<?php

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

//    public function testTrueAssertsToTrue()
//    {
//        $this->assertTrue(false);
//    }

//    public function testThatWeCanGetTheFirstName()
//    {
//        $user = new \Test\TestClass\User();
//
//        $user->setName('ali');
//
//        $this->assertEquals($user->getName(), 'ali');
//    }

    public function testThatWeCanGetFromTable()
    {
        echo \MkOrm\Utils\Utils::deCamelize("OkNo");
//        (\Dotenv\Dotenv::create(__DIR__ . '/../../'))->overload();

//        $user = new MkOrm\Test\TestClass\Users();
//        for ($i = 0; $i <= 50; $i++) {
//            $user->setName("test$i");
//            $user->setPassword("pass$i");
//        }

//        $user->setName('testtest');
//        $user->setPassword('tdasdsadasd');
//        $user->paginate(2);
//        $users = $user->findLike(['name' => 'al']);

//        $users = $user->paginate(1, 20, ['name' => 'ali']);

//        $user->save();
//        $users = $user->findOneLike(['name'=>'al']);
//        echo json_encode(new \MkOrm\Test\TestClass\UsersResource($users));
//        echo var_export($users, true);
    }
}
