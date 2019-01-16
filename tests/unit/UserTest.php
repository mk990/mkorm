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

        $user = new \Test\TestClass\Users();
//        for ($i = 0; $i <= 50; $i++) {
//            $user->setName("test$i");
//            $user->setPassword("pass$i");
//        }

//        $user->setName('testtest');
//        $user->setPassword('tdasdsadasd');
//        $user->paginate(2);
        $user->findOneLike(['name' => 'al']);

//        $users = $user->paginate();

//        $user->save();
//        $users = $user->find([1, 2, 3, 4]);
        echo json_encode($user->toResource(\Test\TestClass\UserResource::class));
//        echo var_export($users,true);
    }
}
