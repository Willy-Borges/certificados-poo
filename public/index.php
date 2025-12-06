<?php
//public/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../core/Autoload.php';

$rotas = require __DIR__ . '/../rotas/web.php';

require_once __DIR__ . '/../core/Roteador.php';

Roteador::carregar($rotas);