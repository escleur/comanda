<?php

namespace Enum;



class TipoComida// extends Enum
{
 
    const BAR = 1;
    const CERVECERIA = 2;
    const COCINA = 3;
    const CANDYBAR = 4;

    public static function IsValid($elem)
    {
        switch ($elem) {
            case "BAR":
                return true;
            case "CERVECERIA":
                return true;
            case "COCINA":
                return true;
            case "CANDYBAR":
                return true;
            default:
                return false;
        }
    }

    public static function GetDescription($state)
    {
        switch ($state) {
            case TipoComida::BAR:
                return "BAR";
            case TipoComida::CERVECERIA:
                return "CERVECERIA";
            case TipoComida::COCINA:
                return "COCINA";
            case TipoComida::CANDYBAR:
                return "CANDYBAR";
            }
    }

    public static function GetVal($state)
    {
        switch ($state) {
            case "BAR":
                return TipoComida::BAR;
            case "CERVECERIA":
                return TipoComida::CERVECERIA;
            case "COCINA":
                return TipoComida::COCINA;
            case "CANDYBAR":
                return TipoComida::CANDYBAR;
            default:
                return false;
        }
    }
}
