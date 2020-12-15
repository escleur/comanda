<?php

namespace Enum;


class UserState
{
    const ACTIVO = 1;
    const SUSPENDIDO = 2;
    const BORRADO = 3;

    public static function IsValid($state)
    {
        switch ($state) {
            case "ACTIVO":
                return true;
            case "SUSPENDIDO":
                return true;
            case "BORRADO":
                return true;
            default:
                return false;
        }
    }

    public static function GetDescription($state)
    {
        switch ($state) {
            case UserState::ACTIVO:
                return "ACTIVO";
            case UserState::SUSPENDIDO:
                return "SUSPENDIDO";
            case UserState::BORRADO:
                return "BORRADO";
        }
    }

    public static function GetVal($role)
    {
        switch ($role) {
            case "ACTIVO":
                return UserState::ACTIVO;
            case "SUSPENDIDO":
                return UserState::SUSPENDIDO;
            case "BORRADO":
                return UserState::BORRADO;
            default:
                return false;
        }
    }
}
