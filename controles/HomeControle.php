<?php
// controles/HomeControle.php

class HomeControle
{
    public function index()
    {
        $pagina = "home/index.php";
        require __DIR__ . '/../views/layouts/principal.php';
    }
}
