<?php
namespace App;

class Request
{
  public function show()
  {
        echo 'Hello World';
   }
   
 private function _requestStatus($code)
 {
  $status = [ 
        200 => 'OK',
        201 => 'Created',
        400 => 'Bad Request',   
        401 => 'Unauthorized',   
        404 => 'Not Found',   
        403 => "Forbidden You don't have permission to access / on this server",   
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        500 => 'Internal Server Error',
   ]; 
  return ($status[$code])?$status[$code]:$status[500]; 
 }
    
 public static function response( $message='' ,$status = 200, Array $allow_methods = NULL)
 {
        http_response_code($status);
  header("HTTP/1.1 " . $status . " " . static::_requestStatus($status));
  if ($allow_methods)
  {header("Allow: " . implode(', ', $allow_methods));}

  if($message) 
  {return $message;}     
//        return json_encode($data);
 }
 
 public static function requireAuthentication($message='')
 {
  $msg= ($message)?" realm=\"$message\"":'';
  header("WWW-Authenticate: Basic$msg");
   static::response('',401);
  die( 'The authentication is required');
 }

 public static function process(Array $routeInfo)
 {
//  $handler = $routeInfo[1];
  $vars = $routeInfo[2];
  
  if(is_array($routeInfo[1]))
  {$handler = [array_shift($routeInfo[1]),array_shift($routeInfo[1])];}
  else {$handler = $routeInfo[1];}
  
  if(is_callable($handler))
  {
   if(is_array($handler))
   {call_user_func_array("{$handler[0]}::{$handler[1]}", $vars);}
   else
   {call_user_func_array($handler, $vars);}
  }
  else
  {echo static::response("Objetc NOT Found", 500);}
 }
}