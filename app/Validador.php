<?php
// app/Validador.php

class Validador
{
    public static function vazio($valor)
    {
        return (!isset($valor) || trim($valor) === '');
    }

    public static function inteiro($valor)
    {
        return filter_var($valor, FILTER_VALIDATE_INT) !== false;
    }

    public static function arrayInt($arr)
    {
        if (!is_array($arr)) return false;

        foreach ($arr as $v) {
            if (!self::inteiro($v)) return false;
        }
        return true;
    }

    public static function data($valor)
    {
        return (bool) strtotime($valor);
    }

    public static function hora($valor)
    {
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $valor);
    }
}
