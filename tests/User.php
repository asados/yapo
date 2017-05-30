<?php

namespace App\Test;

use PHPUnit\Framework\TestCase;
use App\User;

class UserAlter extends \App\User
{
 const FILE_SAVE = 'page'.DIRECTORY_SEPARATOR.'usr_test.info';
 
 public static function findUser($username)
 {return parent::findUser($username);}
 
 public function getPassword()
 {return parent::getPassword();}
 
 public static function getFileName()
 {return parent::getFileName();}
}

//extends \PHPUnit_Framework_TestCase 
class UserTest extends TestCase
{
 protected $username, $rol, $password;
 
 public function testCreatedEmpty()
 {
  $this->assertInstanceOf(User::class,new User());
 }
 
 public function testProperty()
 {
  $user = new UserAlter();
  $expected = ['username'=>'test1','rol'=>'testrol', 'pass'=>'pass_test' ];
  
  $user ->setUsername($expected['username']);
  $user ->setRol($expected['rol']);
  $user ->setPassword($expected['pass']);
  
  $resul = ['username'=> $user ->getUsername(),'rol'=>$user ->getRol(), 'pass'=>$user ->getPassword() ];
  $this->assertSame($expected, $resul);
 }
 
 public function testPropertyCaseInsensitive()
 {
  $user = new User();
  $expected = ['username'=>'test1','rol'=>'testrol' ];
  
  $user ->setUsername('tESt1');
  $user ->setRol('tEstROL');
  
  $resul = ['username'=> $user ->getUsername(),'rol'=>$user ->getRol()];
  $this->assertSame($expected, $resul);
 }
 public function testCreateFirtsUser()
 {
  $filename = UserAlter::getFileName();
  if(file_exists($filename))
  {unlink($filename);}
  
  $expected = ['user'=>'test1','rol'=>'testrol', 'password'=>'pass_test' ];
  
  $user = new UserAlter();
  $user ->setUsername($expected['user']);
  $user ->setRol($expected['rol']);
  $user ->setPassword($expected['password']);
  $user->save();
  
 
  $result =UserAlter::findUser($expected['user']);
  $this->assertSame($expected, $result);
 }
 
 /**
  * @depends testCreateFirtsUser
  */
 public function testAuthenticationFail()
 {
  $this->expectException(\App\Exception::class);
  $user = new UserAlter('test1','FAIL');
 }
 
 /**
  * @depends testCreateFirtsUser
  */
 public function testAuthenticationOk()
 {
  $user = new UserAlter('test1','pass_test');
  $this->assertTrue(TRUE);
 }
 
 /**
  * @depends testCreateFirtsUser
  */
 public function testRemoveUser()
 {
  $user = new UserAlter('test1','pass_test');
  $user->remove();
  $user->save();
  
  $result =UserAlter::findUser('test1');
  
  $this->assertEmpty($result);
 }
 
 
 
 
}
