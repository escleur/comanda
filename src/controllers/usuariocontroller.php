<?php

namespace App\Controllers;
use App\Middlewares\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Usuario;

class UsuarioController{
    public function getAll(Request $request, Response $response, $args){
        //$rta = Usuario::get();
        $response->getBody()->write("hello world");//json_encode($rta));
        return $response;
    }
    public function getOne(Request $request, Response $response, $args){
        $rta = Usuario::find($args['id']);
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        $user = new Usuario;

        $user->nombre = $request->getParsedBody()['nombre'] ?? '';
        $user->email = $request->getParsedBody()['email'] ?? '';
        $user->tipo =  $request->getParsedBody()['tipo'] ?? '';
        $user->clave =  $request->getParsedBody()['clave'] ?? '';;

        $rta = $user->save();

        
        $response->getBody()->write(json_encode($rta));
        return $response;

    }

    //paso por x-www-form-urlencoded
    /*public function updateOne(Request $request, Response $response, $args){
        $user = Usuario::find($args['id']);
        $user->email = $request->getParsedBody()['email'] ?? $user->email;
        $user->tipo =  $request->getParsedBody()['tipo'] ?? $user->tipo;
        $user->password =  $request->getParsedBody()['password'] ?? $user->password;;

        $rta = $user->save();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }
    public function deleteOne(Request $request, Response $response, $args){
        $user = Usuario::find($args['id']);
        $rta = false;
        if($user)
            $rta = $user->delete();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }*/

    public function login(Request $request, Response $response, $args){
        $nombre = $request->getParsedBody()['nombre'] ?? '';
        $legajo = $request->getParsedBody()['legajo'] ?? '';
        $rta = Usuario::where('legajo',$legajo)->first();

        if(strcmp($nombre, $rta['nombre']) == 0){
            $token = Auth::SignIn(['tipo'=>$rta['tipo'],'legajo'=>$rta['legajo']]);
            $response->getBody()->write($token);
        }else{
            $response->getBody()->write("Error al loguear.");
        }
        return $response;
    }


}







