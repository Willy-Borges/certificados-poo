<?php
// controles/UsuarioControle.php

class UsuarioControle
{
    public function index()
    {
        $pagina = 'emitir/formulario.php';
        require __DIR__ . '/../views/layouts/principal.php';
    }
}
