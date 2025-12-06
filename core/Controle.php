<?php
// core/Controle.php

class Controle
{
    
    protected function view(string $view, array $data = [])
    {
        if (!empty($data)) {
            extract($data, EXTR_OVERWRITE);
        }

        $pagina = $view . '.php';

        require_once __DIR__ . '/../views/layouts/principal.php';
    }
}
