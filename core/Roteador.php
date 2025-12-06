<?php
// core/Roteador.php

class Roteador
{
    public static function carregar($rotas)
    {
        $url = $_GET['url'] ?? 'home';

        if (isset($rotas[$url])) {
            $acao = $rotas[$url];
            [$controle, $metodo] = explode('@', $acao);

            $obj = new $controle();
            return $obj->$metodo();
        }

        echo "Rota não encontrada";
        return false;
    }
}
