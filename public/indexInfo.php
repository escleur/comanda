<?php
//https://packagist.org/packages/illuminate/database
//composer require illuminate/database
//composer require "illuminate/events"
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
$database = new Database();

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $rta = Usuario::get();
    $rta = Usuario::where('id', '>', 4)
    ->where('tipo', '=', 2)
    ->whereRaw("select * from")
    ->get();

    $rta = Usuario::where('id', '>', 4)
    ->first();
    $rta = Usuario::find(1);
    $rta = Usuario::find([1,2,3]);
    $rta = Usuario::where('id','333331')->firstOrFail();



    $response->getBody()->write(json_encode($rta));
//alta
    $user = new Usuario;
    $user->usuario = 'pepe';
    $user->email = "dsdkds@dfs.com";

    $rta = $user->save();

//modif

$user = Usuario::find(10);

$user->usuario = 'pepe';
$user->email = "dsdkds@dfs.com";

$rta = $user->save();


$user = Usuario::find(10);

rta = $user->delete();

    return $response;
});

$app->run();





$app->group('/user',function(RouteCollectorProxy $group){
    $group->get('/{id}',function(Request $request, Response $response, $args){
        $body = json_encode(($args));
        $response->getBody()->write($body);
        return $response;
    });
    $group->get('/{id}',function(Request $request, Response $response, $args){
        $body = json_encode(($args));
        $response->getBody()->write($body);
        return $response;
    });
    $group->post('/',function(Request $request, Response $response, $args){
        $response->getBody()->write("POST usuario");
        return $response;
    });
});

