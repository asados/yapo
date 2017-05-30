<?php

namespace App;

class User
{
 protected $username, $rol, $password, $del=FALSE;
 const FILE_SAVE = 'page'.DIRECTORY_SEPARATOR.'usr.info';


 public function __construct($username=NULL, $password=NULL)
 {
  $username=($username)? mb_strtolower(trim($username), 'UTF-8'):FALSE;
  $password= ($password)?trim($password):NULL;
  $this->username ='';
  $this->rol = '';
  
  if($username)
  {
   $usr_info = static::findUser($username);
   $this->factory($usr_info);
   
   if(!$usr_info || !$this->authenticate($password))
  {throw new Exception('Athentication fail');}
  }
 }
 
 protected function authenticate($passCheck)
 {return ($this->password == $passCheck);}
 
 protected function factory(Array $info)
 {
  $this->username = (isset($info['user']))?$info['user']:'';
  $this->rol = (isset($info['rol']))?$info['rol']:'';
  $this->password = (isset($info['password']))?$info['password']:'';
 }
 
 protected static function findUser($username)
 {
  $user_list = static::readDataUser();
  
  return (isset($user_list[$username]))?$user_list[$username]:[];
 }
 
 public static function findUserObject($username)
 {
  $user_info = static::findUser($username);
  if(!$user_info){return NULL;}
  
  $user = new self();
  $user->factory($user_info);
  return ($user);
 }
 
 public static function getAllUser()
 {
  $lis_user = static::readDataUser();
  
  foreach ($lis_user as $k => $v)
  {unset($lis_user[$k]['password']);}// prevent to expose the password
  
  return $lis_user;
 }

 protected static function getFileName()
 {return dirname(__dir__).DIRECTORY_SEPARATOR. static::FILE_SAVE;}
 
  public static function getUser($username)
 {
  $user= static::findUser($username);
  
  if(isset($user['password']))
  {unset($user['password']);} // prevent to expose the password
  
  return $user;
 }
 
 protected function getPassword()
 {return $this->password;}
 
 public function getRol()
 {return $this->rol;}
 
 public function getUsername()
 {return $this->username;}
 
 public function isAdminRol()
 {return $this->getRol() == 'admin';}
 
 protected static function readDataUser()
 {
  $file_user= static::getFileName();
  $user_list= [];
  
  if(file_exists($file_user) && ($inf = unserialize(file_get_contents($file_user))) && is_array($inf)) // intentional assignment
  {$user_list= $inf;}

  return $user_list;
 }
 
 public function remove()
 {$this->del=TRUE;}
 
 public function save()
 {
  if(!$this->getUsername() || !$this->getRol() || !$this->getPassword())
  {throw new Exception('Imconplete information');}
  $usr_list = static::readDataUser();
  $this->writeDataUser($usr_list);
 }
 
 public function setPassword($password)
 {$this->password=$password;}

 public function setRol($rol)
 {$this->rol= mb_strtolower(trim($rol));}
 
 public function setUsername($username)
 {$this->username=mb_strtolower(trim($username));} 
 
 protected function writeDataUser(Array $userData)
 {
  $file_user= static::getFileName();
  $user_list= (!is_null($userData))?$userData:[];
  
  if($this->del)
  {unset($user_list[$this->username]);}
  else
  {$user_list[$this->username]= ['user'=>$this->username, 'rol'=>$this->rol, 'password'=> $this->password];}
  
  file_put_contents($file_user,serialize($user_list));
 }
}