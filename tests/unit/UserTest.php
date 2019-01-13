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
        $user = new \Test\TestClass\User();

//        $user->find();

//        $user->find(9)->delete();
//        $user->setName('test1');
//        $user->setPassword('pass1');

//        if (!$result = $user->save()) {
//            echo 'no';
//        }
        echo json_encode( $user->find(7)->delete());
    }
}
