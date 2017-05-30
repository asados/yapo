<?php
namespace App;

class Controller
{
 const LOGIN_PAGE='page/login.php';
 const SESSION_TIMEOUT=60*5; // 5 minutes

 public static function addUser()
 {
  $username = (isset($GLOBALS['_POST']['username']))?trim(mb_strtolower($GLOBALS['_POST']['username'])):NULL;
  $rol = (isset($GLOBALS['_POST']['rol']))?trim(mb_strtolower($GLOBALS['_POST']['rol'])):NULL;
  $password = (isset($GLOBALS['_POST']['password']))?trim($GLOBALS['_POST']['password']):NULL;
  
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  
  if(!$username || !$rol || !$password)
  {echo json_encode(['error'=>'User already exist']);return;}
  
  if(\App\User::findUserObject($username))
  {echo json_encode(['error'=>'User already exist']);return;}
  
  $user = new \App\User();
  $user->setUsername($username);
  $user->setRol($rol);
  $user->setPassword($password);
  $user->save();
  
  echo json_encode(['mesaage'=> 'User Created']);
 }

 public static function changeUserPassword($username, $password)
 {
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  
  if(!($user =\App\User::findUserObject($username))) //intentional assignment
  {echo json_encode(['error'=>'User not exist']);return;}
  
  $user->setPassword($password);
  $user->save();
  echo json_encode(['mesaage'=> 'Password changed']);
 }
 
 public static function changeUserRol($username, $rol)
 {
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  
  if(!($user = \App\User::findUserObject($username))) //intentional assignment
  {echo json_encode(['error'=>'User not exist']);return ;}
  
  $user->setRol($rol);
  $user->save();
  echo json_encode(['mesaage'=> 'Rol changed']);
 }
 
 public static function fail()
 {static::servePage('page/fail.php');}
 
 public static function getAllUser()
 {
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  echo json_encode(\App\User::getAllUser());
 }
 
 public static function getUser($username)
 {
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  echo json_encode(\App\User::getUser($username));
 }
 
 public static function home()
 {header('location: /login');}
 
 protected static function login($username,$password,&$session)
 {
  $page_redirec = ['page_1'=>'page1','page_2'=>'page2','page_3'=>'page3', 'fail'=>'fail'];
  try
  {
   $user = new \App\User($username,$password);
   $session['_USER_']=$user;
   $session['discard_after'] = (time() + static::SESSION_TIMEOUT); // 5 minutes 
   if(isset($page_redirec[$user->getRol()]))
   {return $page_redirec[$user->getRol()];}
   else
   {return $page_redirec['fail'];}
  }
  catch (Exception $exc)
  {return NULL;}
 }

 public static function loginGet()
 {
  if(static::sessionActive($GLOBALS['_COOKIE']))
  {
   session_start();
   if(static::sessionExist($GLOBALS['_SESSION']))
   {static::sessionStop();}
  }
  
  static::servePage(static::LOGIN_PAGE);
 }
 
 public static function loginPost()
 {
  $username = (isset($GLOBALS['_POST']['username']))?$GLOBALS['_POST']['username']:NULL;
  $password = (isset($GLOBALS['_POST']['password']))?$GLOBALS['_POST']['password']:NULL;
  
  session_start();
  if(static::sessionExist($GLOBALS['_SESSION']))
  {
   static::sessionStop();
   session_start();
  }
  
  if($username && $password && ($redirec_page = static::login($username,$password,$GLOBALS['_SESSION'])) )//intentional assignment
  {header ("location: /$redirec_page");}
  else
  {header('location: /login?fail=1');}
 }
 
 public static function logOut()
 {
  if(static::sessionActive($GLOBALS['_COOKIE']))
  {
   session_start();
   static::sessionStop();
  }
   
  header('location: /login');
 }

 protected static function page($rol, $page)
 {
  //If not exist the cookie's ID session, will go to login page
  if(!static::sessionActive($GLOBALS['_COOKIE']))
  {
   header('location: /login');
   return;
  }
  
  session_start();
  if(($user= static::sessionExist($GLOBALS['_SESSION']))) //intentional assignment
  {
   if($user->getRol() != $rol)
   {
    echo \App\Request::response("Forbidden You don't have permission to access this page",403);
    return;
   }
    
   if(time() > $GLOBALS['_SESSION']['discard_after'])
   {
     header('location: /logout');
     return;
   }
   $GLOBALS['_SESSION']['discard_after'] = (time() + static::SESSION_TIMEOUT); // assign 5 more minutes
    $GLOBALS['_USER_']=$user;
    static::servePage($page);
  }
  else
  {header('location: /login');}
 }

 public static function page1()
 {static::page('page_1','page/page1.php');}

 
 public static function page2()
 {static::page('page_2','page/page2.php');}

 
 public static function page3()
 {static::page('page_3','page/page3.php');}
 
 public static function removeUser($username)
 {
  static::validateAutentication($GLOBALS['_SERVER']);
  header("Content-Type: application/json");
  
  if(!($user = \App\User::findUserObject($username))) //intentional assignment
  {echo json_encode(['error'=>'User not exist']);return ;}
  
  $user->remove();
  $user->save();
  echo json_encode(['mesaage'=> 'User removed']);
 }
 
  protected static function servePage($page)
 {
  $page_use = dirname(__DIR__) . DIRECTORY_SEPARATOR . $page;
  if(!file_exists($page_use))
  {
   echo \App\Request::response('Page Not Found',404);
   return;
  }
   
  if(!is_readable($page_use))
  {
   echo \App\Request::response("The page isn't readable", 500);
   return;
  }
   
  include $page_use;
 }
 
 public static function sessionActive(Array &$cookie)
 {return (isset($cookie[session_name()]) && $cookie[session_name()]) ;}
  
 public static function sessionExist(Array &$session)
 {
  if(!isset($session['_USER_']) || !($session['_USER_'] instanceof \App\User))
  {return NULL;}
   
  return $session['_USER_'];
 }
 
 protected static function sessionStop()
 {
  session_destroy();
  session_unset();
  setcookie(session_name(),'',0,'/');
 }
 
 public static function validateAutentication(Array &$server)
 {
  if (!isset($server['PHP_AUTH_USER']))
  {\App\Request::requireAuthentication();}
  else
  {
   $user_check=trim(mb_strtolower($server['PHP_AUTH_USER']));
   $pass_check=trim($server['PHP_AUTH_PW']);
   /*
   try 
   {$user= new \App\User($user_check, $pass_check);} //intentional assignment
   catch (Exception $exc)
   {$user = NULL;}
   
   if(!$user || !($user->isAdminRol()))
   {\App\Request::requireAuthentication('Login or password Fail');}
*/
   if($user_check!='admin' ||$pass_check!='pass')
   {\App\Request::requireAuthentication('Login or password Fail');}
  }
 }
 
}