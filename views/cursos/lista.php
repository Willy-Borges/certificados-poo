<?php
// /views/cursos/lista.php

$cursos_exemplo = [
    ["nome" => "Atendimento ao Cliente",      "img" => "arquivos/img/atendimento-ao-cliente.jpg"],
    ["nome" => "Empreendedorismo",            "img" => "arquivos/img/empreendedorismo.jpg"],
    ["nome" => "Redes",                       "img" => "arquivos/img/redes.jpg"],
    ["nome" => "Excel Intermediário",         "img" => "arquivos/img/excel.jpg"],
    ["nome" => "Lógica de Programação",       "img" => "arquivos/img/logica-de-programacao.jpeg"]
];







$lista = !empty($cursos) ? $cursos : $cursos_exemplo;
?>

<div class="container-cursos-grid">

    <?php foreach ($lista as $c): ?>
        <?php
            $nome = is_array($c) ? ($c['nome'] ?? '') : (string)$c;

            
            $imagem = $c['img'] ?? "/certificados-poo/arquivos/img/default.png";
        ?>

        <div class="card-curso">
            <img src="<?= $imagem ?>" class="img-curso" alt="<?= htmlspecialchars($nome) ?>">

            <div class="curso-info">
                <p class="curso-nome"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></p>

                <div class="carga">
                    <span class="tooltip-horas">
                    <img src="/certificados-poo/arquivos/img/relogio.png" class="icone-relogio" alt="">
                    <span class="tooltip-text">Mínimo de 2 horas para emitir o certificado.</span>
                    </span>

                    <span>2h</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>
