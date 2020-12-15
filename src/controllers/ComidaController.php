<?php

namespace Controllers;
use Middlewares\Auth;
use Components\GenericResponse;

use Enum\TipoComida;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Models\Comida;

class ComidaController{
    public function getAll(Request $request, Response $response, $args){
        try{
            $rta = Comida::get();
            $response->getBody()->write(GenericResponse::obtain(true, "Listado", $rta));

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }

        return $response;
    }
    public function getOne(Request $request, Response $response, $args){
        try{
            $rta = Comida::find($args['id']);
            if(empty($rta)){
                throw new \Exception("La comida no existe");
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
            $comida = new Comida;

            $comida->nombre = $request->getParsedBody()['nombre'] ?? '';
            $comida->tipo = $request->getParsedBody()['tipo'] ?? '';
            $comida->precio = $request->getParsedBody()['precio'] ?? '';
            if(!TipoComida::IsValid(TipoComida::getDescription($comida->tipo))){
                throw new \Exception("La comida tiene un tipo erroneo");
            }
            if(!is_numeric($comida->precio)){
                throw new \Exception("El precio debe ser un valor numerico");
            }

            $rta = $comida->save();
            $response->getBody()->write(GenericResponse::obtain(true, $rta, null));

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), $comida));
            $response->withStatus(500);
        }
    
        return $response;

    }

    //paso por x-www-form-urlencoded
    public function updateOne(Request $request, Response $response, $args){
        try{
            $mesa = Mesa::find($args['id']);

            $comida->nombre = $request->getParsedBody()['nombre'] ?? $comida->nombre;
            $comida->tipo = $request->getParsedBody()['tipo'] ?? $comida->tipo;
            $comida->precio = $request->getParsedBody()['precio'] ?? $comida->precio;
            if(!TipoComida::IsValid(TipoComida::getDescription($comida->tipo))){
                throw new \Exception("La comida tiene un tipo erroneo");
            }
            if(!is_numeric($comida->precio)){
                throw new \Exception("El precio debe ser un valor numerico");
            }

            $rta = $comida->save();
            $response->getBody()->write(GenericResponse::obtain(true, "Modificado exitosamente", $rta));


        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), $comida));
            $response->withStatus(500);
        }
        return $response;
    }
    public function deleteOne(Request $request, Response $response, $args){
        try{
            $comida = Comida::find($args['id']);
            $rta = false;
            if($comida){
                $rta = $comida->delete();
            }else{
                throw new \Exception("La comida no se encontro");
            }

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
        return $response;
    }


}







