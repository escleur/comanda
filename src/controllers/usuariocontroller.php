<?php

namespace Controllers;
use Middlewares\Auth;
use Components\GenericResponse;

use Enum\UserRole;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Models\Usuario;

class UsuarioController{
    public function getAll(Request $request, Response $response, $args){
        try{
            $rta = Usuario::get();
            $response->getBody()->write(GenericResponse::obtain(true, "Listado", $rta));

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }

        return $response;
    }
    public function getOne(Request $request, Response $response, $args){
        try{
            $rta = Usuario::find($args['id']);
            if(empty($rta)){
                throw new \Exception("El usuario no existe");
            }
            $response->getBody()->write(GenericResponse::obtain(true, "Item solicitado", $rta));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function addOne(Request $request, Response $response, $args){
        try{
            $user = new Usuario;

            $user->nombre = $request->getParsedBody()['nombre'] ?? '';
            $user->tipo =  $request->getParsedBody()['tipo'] ?? '';
            $user->email = strtolower( $request->getParsedBody()['email']) ?? '';
            $user->password =  $request->getParsedBody()['password'] ?? '';

            if(UserRole::IsValidArea(UserRole::getDescription($user->tipo))){
                $rta = $user->save();
                $response->getBody()->write(GenericResponse::obtain(true, $rta, null));
            }else{
                throw new \Exception("El usuario tiene un tipo erroneo");
            }

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
    
        return $response;

    }

    //paso por x-www-form-urlencoded
    public function updateOne(Request $request, Response $response, $args){
        try{
            $user = Usuario::find($args['id']);

            $user->nombre = $request->getParsedBody()['nombre'] ?? $user->nombre;
            $user->tipo =  $request->getParsedBody()['tipo'] ?? $user->tipo;
            $user->email = strtolower( $request->getParsedBody()['email']) ?? $user->email;
            $user->password =  $request->getParsedBody()['password'] ?? $user->password;


            if(UserRole::IsValidArea(UserRole::getDescription($user->tipo))){
                //$user->tipo = UserRole::GetVal($user->tipo);
                $rta = $user->save();
                $response->getBody()->write(GenericResponse::obtain(true, "Modificado exitosamente", $rta));
            }else{
                throw new \Exception("El usuario tiene un tipo erroneo");
            }

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), $user));
            $response->withStatus(500);
        }
        return $response;
    }
    public function deleteOne(Request $request, Response $response, $args){
        try{
            $user = Usuario::find($args['id']);
            $rta = false;
            if($user){
                $rta = $user->delete();
            }else{
                throw new \Exception("El usuario no se encontro");
            }

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
        return $response;
    }



}







