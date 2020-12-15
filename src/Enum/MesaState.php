<?php

namespace Enum;


class MesaState// extends Enum
{
    // Socio.

    const ESPERANDO = 1;
    const COMIENDO = 2;
    const PAGANDO = 3;
    const CERRADA = 4;

    public static function IsValid($elem)
    {
        switch ($elem) {
            case "ESPERANDO":
                return true;
            case "COMIENDO":
                return true;
            case "PAGANDO":
                return true;
            case "CERRADA":
                return true;
            default:
                return false;
        }
    }

    public static function GetDescription($state)
    {
        switch ($state) {
            case MesaState::ESPERANDO:
                return "ESPERANDO";
            case MesaState::COMIENDO:
                return "COMIENDO";
            case MesaState::PAGANDO:
                return "PAGANDO";
            case MesaState::CERRADA:
                return "CERRADA";
            }
    }

    public static function GetVal($state)
    {
        switch ($state) {
            case "ESPERANDO":
                return MesaState::ESPERANDO;
            case "COMIENDO":
                return MesaState::COMIENDO;
            case "PAGANDO":
                return MesaState::PAGANDO;
            case "CERRADA":
                return MesaState::CERRADA;
            default:
                return false;
        }
    }
}
