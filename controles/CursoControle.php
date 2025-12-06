<?php
// controles/CursoControle.php

require_once __DIR__ . '/../modelos/Curso.php';

class CursoControle
{
    public function lista()
    {
        $curso = new Curso();
        $cursos = $curso->todos();

        $pagina = "cursos/lista.php";
        require __DIR__ . '/../views/layouts/principal.php';
    }
}
