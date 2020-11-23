<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
// use Psr\Http\Message\ResponseInterface as Response;
use App\Middlewares\Auth;

class AuthMiddleware
{
    public $role;
    public function __construct($role){
        $this->role = $role;
    }

    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaders()['token'][0] ?? '';
        // $_SERVER['HTTP_TOKEN'] ?? '';
        $data = (object) ["tipo"=>""];
        try {
            Auth::Check($token);
            $data = Auth::GetData($token);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
      
       
        $valido = false; // valido token
        //Revisa el array de roles buscando coincidencia
        foreach ($this->role as $key => $value) {
            if(strcmp($value, $data->tipo)==0)
                $valido = true;
        }

        if (!$valido) {
            $response = new Response();
            $response->getBody()->write("Prohibido pasar");
            // throw new \Slim\Exception\HttpForbiddenException($request);
            return $response->withStatus(403);
        } else {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }
    }
}
