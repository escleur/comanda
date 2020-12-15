<?php

namespace Controllers;

use Models\Usuario;
use Models\Login;
use Components\PassManager;
use Components\Auth;
use Components\GenericResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enum\UserState;
class LoginController
{
    public static function login(Request $request, Response $response, $args)
    {
        try {
            $email = $request->getParsedBody()['email'] ?? "";
            $pass =  $request->getParsedBody()['password'] ?? "";

            if (!empty($email) && !empty($pass)) {
                
                /* Crypto */
                //$pass = PassManager::Hash($pass);

                /* Look up for credentials */
                $retrievedUser = Usuario::where('email', $email)->where('password', $pass)->first();

                if ($retrievedUser != null) {
                    if($retrievedUser->estado == UserState::SUSPENDIDO || $retrievedUser->estado == UserState::BORRADO ){
                        $response->getBody()->write(GenericResponse::obtain(false, 'Cuenta desactivada.'));

                    }else{
                        $token = Auth::SignIn(['id'=>$retrievedUser->id,'email'=>$email,'tipo'=>$retrievedUser->tipo]);
                        $response->getBody()->write(GenericResponse::obtain(true, 'Bienvenidx ' . $email, $token));
                        $login = new Login();
                        $login->idUsuario = $retrievedUser->id;
                        $login->save();
                    }
                } else {
                    $response->getBody()->write(GenericResponse::obtain(false, 'Credenciales invalidas.'));
                }
            } else {
                $response->getBody()->write(GenericResponse::obtain(false, 'Debe especificar el campo email y password.'));
                $response->withStatus(401);
            }
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error a la hora de realizar la autenticacion.", null));
            $response->withStatus(500);
        }

        return $response;
    }

    public function getRole(Request $request, Response $response, $args)
    {
        try {
            $token = $request->getHeaders()['token'] ?? "";
            $role = Token::getRole($token);
            $response->getBody()->write(GenericResponse::obtain(true, '', $role));
        } catch (\Exception $e) {
            $response->getBody()->write(GenericResponse::obtain(false, "Error a la hora de obtener el rol del usuario.", null));
        }

        return $response;
    }

    public static function validateToken(Request $request, Response $response, $args)
    {
        $token = $request->getHeaders()['token'] ?? "";

        if (!empty($token)) {
            $isDecoded = Token::validateToken($token);
            $response->getBody()->write(GenericResponse::obtain($isDecoded, $isDecoded ? 'Token valido.' : 'Token ivalido', $token));
        } else {
            $response->getBody()->write(GenericResponse::obtain(false, 'Invalid credentials'));
        }

        return $response;
    }
}
