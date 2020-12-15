<?php

namespace Enum;



class PedidoState// extends Enum
{
    // Socio.

    const PREPARACION = 1;
    const LISTO = 2;
    const CANCELADO = 3;
    const FINALIZADO = 4;

    public static function IsValid($elem)
    {
        switch ($elem) {
            case "PREPARACION":
                return true;
            case "LISTO":
                return true;
            case "CANCELADO":
                return true;
            case "FINALIZADO":
                return true;
            default:
                return false;
        }
    }

    public static function GetDescription($state)
    {
        switch ($state) {
            case PedidoState::PREPARACION:
                return "PREPARACION";
            case PedidoState::LISTO:
                return "LISTO";
            case PedidoState::CANCELADO:
                return "CANCELADO";
            case PedidoState::FINALIZADO:
                return "FINALIZADO";
        }
    }

    public static function GetVal($state)
    {
        switch ($state) {
            case "PREPARACION":
                return PedidoState::PREPARACION;
            case "LISTO":
                return PedidoState::LISTO;
            case "CANCELADO":
                return PedidoState::CANCELADO;
            case "FINALIZADO":
                return PedidoState::FINALIZADO;
            default:
                return false;
        }
    }
}
