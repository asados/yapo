<?php
require_once (dirname(__DIR__)) . DIRECTORY_SEPARATOR .'vendor'. DIRECTORY_SEPARATOR .'autoload.php';

$dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {

    $r->addRoute(['GET','POST'], '[/]', ['\App\Controller','home']);
    $r->addRoute('GET', '/login', ['\App\Controller','loginGet']);
    $r->addRoute('POST', '/login', ['\App\Controller','loginPost']);
    $r->addRoute('GET', '/logout', ['\App\Controller','logOut']);
    
    $r->addRoute(['GET','POST'], '/page1[/]', ['\App\Controller','page1']);
    $r->addRoute(['GET','POST'], '/page2[/]', ['\App\Controller','page2']);
    $r->addRoute(['GET','POST'], '/page3[/]', ['\App\Controller','page3']);
    $r->addRoute(['GET','POST'], '/fail[/]', ['\App\Controller','fail']);
    
    $r->addGroup('/admin', function (\FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/user[/]', ['\App\Controller','getAllUser']);
        $r->addRoute('GET', '/user/{name}', ['\App\Controller','getUser']);
        $r->addRoute('DELETE', '/user/{name}', ['\App\Controller','removeUser']);
        $r->addRoute('POST', '/user[/]', ['\App\Controller','addUser']);
        $r->addRoute('PUT', '/rol/{name}/{rol}', ['\App\Controller','changeUserRol']);
        $r->addRoute('PUT', '/password/{name}/{password}', ['\App\Controller','changeUserPassword']);
    });
    
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?'))
{$uri = substr($uri, 0, $pos);}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        echo \App\Request::response('Page Not Found',404);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo \App\Request::response('METHOD NOT ALLOWED',405,$routeInfo[1]);
        break;
    case \FastRoute\Dispatcher::FOUND:
        \App\Request::process($routeInfo);
        break;
}
