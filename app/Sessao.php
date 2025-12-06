<?php
// app/Sessao.php

class Sessao
{
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function definir($chave, $valor)
    {
        self::iniciar();
        $_SESSION[$chave] = $valor;
    }

    public static function obter($chave)
    {
        self::iniciar();
        return $_SESSION[$chave] ?? null;
    }

    public static function apagar($chave)
    {
        self::iniciar();
        if (isset($_SESSION[$chave])) {
            unset($_SESSION[$chave]);
        }
    }

    public static function destruir()
    {
        self::iniciar();
        session_destroy();
    }
}
