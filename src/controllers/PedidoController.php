<?php

namespace Controllers;
use Components\Auth;
use Components\GenericResponse;

use Enum\UserRole;
use Enum\TipoComida;
use Enum\MesaState;
use Enum\PedidoState;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Models\Comida;
use Models\Mesa;
use Models\Pedido;
use Models\PedidoComida;


class PedidoController{
    public function getAll(Request $request, Response $response, $args){
        try{
            $token = $request->getHeaders()['token'][0] ?? '';
            $data = (object) ["tipo"=>""];
            try {
                Auth::Check($token);
                $data = Auth::GetData($token);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
            switch ($data->tipo){
                case UserRole::ADMIN:
                    $rta = Pedido::get();

                break;

                case UserRole::MOZO:
                    $rta = Pedido::where('estado_bar', '=', PedidoState::LISTO)
                    ->where('estado_cerveceria', '=', PedidoState::LISTO)
                    ->where('estado_cocina', '=', PedidoState::LISTO)
                    ->where('estado_candybar', '=', PedidoState::LISTO)
                    ->get();

                break;
                case UserRole::BARTENDER:
                    $rta = Pedido::where('estado_bar', '=', PedidoState::PREPARACION)->get();
                break;

                case UserRole::COCINERO:
                    $rta = Pedido::where('estado_cocina', '=', PedidoState::PREPARACION)->
                orWhere('estado_candybar','=', PedidoState::PREPARACION)->get();
                break;

                case UserRole::CERVECERO:
                    $rta = Pedido::where('estado_cerveceria', '=', PedidoState::PREPARACION)->get();

                break;
                default:
                    $rta = "Tiene que loguear";
            }


            $response->getBody()->write(GenericResponse::obtain(true, "Listado", $rta));

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function ready(Request $request, Response $response, $args){
        try{
            $token = $request->getHeaders()['token'][0] ?? '';
            $data = (object) ["tipo"=>""];
            try {
                Auth::Check($token);
                $data = Auth::GetData($token);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
            $pedido = Pedido::where('codigo', '=', $args['pedido'])->get();
            if(empty($pedido)){
                throw new \Exception("El pedido no existe");
            }
                
            $rta = "Pedido listo";
            switch ($data->tipo){
                case UserRole::ADMIN:
                    if($pedido[0]->estado_bar == PedidoState::PREPARACION){
                        $pedido[0]->estado_bar = PedidoState::LISTO;
                    }
                    if($pedido[0]->estado_cocina == PedidoState::PREPARACION){
                        $pedido[0]->estado_cocina = PedidoState::LISTO;
                    }
                    if($pedido[0]->estado_candybar == PedidoState::PREPARACION){
                        $pedido[0]->estado_candybar = PedidoState::LISTO;
                    }
                    if($pedido[0]->estado_cerveceria == PedidoState::PREPARACION){
                        $pedido[0]->estado_cerveceria = PedidoState::LISTO;
                    }
                    
                    break;
                    
                case UserRole::BARTENDER:
                    if($pedido[0]->estado_bar == PedidoState::PREPARACION){
                        $pedido[0]->estado_bar = PedidoState::LISTO;
                    }
                    break;
                            
                case UserRole::COCINERO:
                    if($pedido[0]->estado_cocina == PedidoState::PREPARACION){
                        $pedido[0]->estado_cocina = PedidoState::LISTO;
                    }
                    if($pedido[0]->estado_candybar == PedidoState::PREPARACION){
                        $pedido[0]->estado_candybar = PedidoState::LISTO;
                    }
                    $rta = "Listo cocina";
                    break;
                                
                case UserRole::CERVECERO:
                    if($pedido[0]->estado_cerveceria == PedidoState::PREPARACION){
                        $pedido[0]->estado_cerveceria = PedidoState::LISTO;
                    }
                    
                    break;
                default:
                $rta = "Tiene que loguear";
            }
            $pedido[0]->save();
            
            $response->getBody()->write(GenericResponse::obtain(true, $rta, $pedido));
            
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
        
        return $response;
    }
                    
    public function tiempoEstimado(Request $request, Response $response, $args){
        try{
            $mesa = Mesa::where('codigo', '=', $args['mesa'])->first();
            if(empty($mesa)){
                throw new \Exception("La mesa no existe");
            }
            $rta = Pedido::where("idMesa", '=', $mesa->id)->where("codigo", "=", $args['pedido'])->first();
            if(empty($rta)){
                throw new \Exception("El pedido no existe");
            }
            $fecha = $rta->created_at;
            $strfecha = $fecha->format('Y-m-d H:i:s');
            $strNow = date("Y-m-d H:i:s");
            $minutos = floor((strtotime($strNow)-strtotime($strfecha))/60);
            if($minutos > $rta->tiempoEstipulado){
                throw new \Exception("Tiempo exedido");

            }else{
                $response->getBody()->write(GenericResponse::obtain(true, "Su pedido estara listo en " + $minutos - $rta->tiempoEstipulado, $rta));

            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }

        return $response;
    }


    public function servido(Request $request, Response $response, $args){
        try{
            $token = $request->getHeaders()['token'][0] ?? '';
            $data = (object) ["tipo"=>""];
            try {
                Auth::Check($token);
                $data = Auth::GetData($token);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
            $pedido = Pedido::where('codigo', '=', $args['pedido'])->get();
            if(empty($pedido)){
                throw new \Exception("El pedido no existe");
            }
            
            $rta = "Solo mozo o admin sirven el pedido";
            switch ($data->tipo){

                case UserRole::ADMIN:
                    
                case UserRole::MOZO:
                    if($pedido[0]->estado_bar == PedidoState::LISTO
                    && $pedido[0]->estado_cocina == PedidoState::LISTO
                    && $pedido[0]->estado_cerveceria == PedidoState::LISTO
                    && $pedido[0]->estado_candybar == PedidoState::LISTO
                    ){
                        $pedido[0]->estado_bar = PedidoState::FINALIZADO;
                        $pedido[0]->estado_cocina = PedidoState::FINALIZADO;
                        $pedido[0]->estado_cerveceria = PedidoState::FINALIZADO;
                        $pedido[0]->estado_candybar = PedidoState::FINALIZADO;
                        $pedido[0]->save();
                        $mesa = Mesa::where("id", '=', $pedido[0]->idMesa)->first();
                        $mesa->estado = MesaState::COMIENDO;
                        $mesa->save();
                        $rta = "Pedido entregado";
                    }
                break;
                    
            }
            
            $response->getBody()->write(GenericResponse::obtain(true, $rta, $pedido));
            
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
        
        return $response;
    }
    public function pagando(Request $request, Response $response, $args){
        try{
            $token = $request->getHeaders()['token'][0] ?? '';
            $data = (object) ["tipo"=>""];
            try {
                Auth::Check($token);
                $data = Auth::GetData($token);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
            $pedido = Pedido::where('codigo', '=', $args['pedido'])->get();
            if(empty($pedido)){
                throw new \Exception("El pedido no existe");
            }
            
            $rta = "Solo mozo o admin cobra el pedido";
            switch ($data->tipo){

                case UserRole::ADMIN:
                    
                case UserRole::MOZO:
                    if($pedido[0]->estado_bar == PedidoState::FINALIZADO
                    && $pedido[0]->estado_cocina == PedidoState::FINALIZADO
                    && $pedido[0]->estado_cerveceria == PedidoState::FINALIZADO
                    && $pedido[0]->estado_candybar == PedidoState::FINALIZADO
                    ){
                        $mesa = Mesa::where("id", '=', $pedido[0]->idMesa)->first();
                        $mesa->estado = MesaState::PAGANDO;
                        $mesa->save();
                        $rta = "Pedido pagado";
                    }
                break;
                    
            }
            
            $response->getBody()->write(GenericResponse::obtain(true, $rta, $pedido));
            
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
        
        return $response;
    }       
    public function addOne(Request $request, Response $response, $args){
        try{
            $pedido = new Pedido;
            
            $pedido->nombreCliente = $request->getParsedBody()['nombre'] ?? '';
            $codigoMesa = $request->getParsedBody()['codigoMesa'];
            if (!Mesa::where('codigo', '=', $codigoMesa)->exists()) {
                throw new \Exception("El codigo de mesa no existe");
            }

            $mesa = Mesa::where('codigo', $codigoMesa)->first();

            if($mesa->estado == MesaState::ESPERANDO || $mesa->estado == MesaState::COMIENDO){
                throw new \Exception("La mesa esta ocupada");
            }
            $mesa->estado = MesaState::ESPERANDO;
            $mesa->save();

            $pedido->idMesa = $mesa->id;

            $pedido->estado_cocina = PedidoState::LISTO;
            $pedido->estado_bar = PedidoState::LISTO;
            $pedido->estado_cerveceria = PedidoState::LISTO;
            $pedido->estado_candybar = PedidoState::LISTO;
            $pedido->tiempoEstipulado = $request->getParsedBody()['tiempo'] ?? '';
            $pedido->tiempoEntrega = -1;

            $pedido->save();
            $codigo = substr(md5($pedido->id), 5, 5);
            $pedido->codigo = $codigo;

            $orden = $request->getParsedBody()['orden'] ?? '';
            foreach($orden as $valor){
                
                if($valor['comida'] && $valor['cantidad']){
                    if(Comida::where('id', '=', $valor['comida'])->exists()){

                        $pedidoComida = new PedidoComida;
                        $pedidoComida->idPedido = $pedido->id;
                        $pedidoComida->idComida = $valor['comida'];
                        $pedidoComida->cantidad = $valor['cantidad'];
                        $pedidoComida->save();
                        $comida = Comida::where('id', '=', $valor['comida'])->first();
                        switch($comida->tipo){
                            case TipoComida::BAR:
                                $pedido->estado_bar = PedidoState::PREPARACION;
                            break;
                            case TipoComida::CERVECERIA:
                                $pedido->estado_cerveceria = PedidoState::PREPARACION;
                            break;
                            case TipoComida::COCINA:
                                $pedido->estado_cocina = PedidoState::PREPARACION;
                            break;
                            case TipoComida::CANDYBAR:
                                $pedido->estado_candybar = PedidoState::PREPARACION;
                        }
                    }
                } 
            }
            $pedido->save();
            $response->getBody()->write(GenericResponse::obtain(true, "Pedido agregado", $pedido));

        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, $e->getMessage(), null));
            $response->withStatus(500);
        }
    
        return $response;

    }

 
}







