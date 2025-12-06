<?php
// views/certificados/lista.php

$certificados = $certificados ?? [];
?>

<div class="emitir-form emitir-certificados">

  <div class="emitir-columns emitir-columns-cert">

    <div class="emitir-header emitir-header-cert">
      <div class="titulo-coluna">Cursos</div>
      <div class="titulo-coluna">Carga Horária</div>
    </div>

    <div class="emitir-list emitir-list-cert">

      <?php if (!empty($certificados)): ?>

        <?php foreach ($certificados as $c): ?>
          <div class="linha-completa linha-completa-cert">

            <!-- NOME DO CURSO -->
            <div class="col-nome col-nome-cert">
              <?= htmlspecialchars($c['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>

            <!-- COLUNA DO RELÓGIO + TEXT + BOTÃO -->
            <div class="col-check col-check-cert">

              <img src="/certificados-poo/arquivos/img/relogio.png"
                   class="icone-relogio-emit"
                   alt="Relógio">

              <div class="linha-carga linha-carga-cert" role="filled">
                <?= htmlspecialchars($c['carga'] ?? '', ENT_QUOTES, 'UTF-8') ?>
              </div>

              <!-- BOTÃO IMPRIMIR -->
              <button class="btn-imprimir"
              onclick="window.open('/certificados-poo/public/?url=certificado/visualizar&id=<?= $c['id'] ?>', '_blank')">


                <img src="/certificados-poo/arquivos/img/botao-imprimir.png"
                     alt="Imprimir Certificado">
              </button>

            </div>
          </div>
        <?php endforeach; ?>

      <?php else: ?>

        <!-- PRIMEIRA LINHA VAZIA -->
        <div class="linha-completa linha-completa-cert primeiro">
          <div class="col-nome col-nome-cert"></div>

          <div class="col-check col-check-cert">
            <img src="/certificados-poo/arquivos/img/relogio.png" class="icone-relogio-emit">
            <div class="linha-carga linha-carga-cert"></div>
          </div>
        </div>

        <!-- OUTRAS LINHAS VAZIAS -->
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div class="linha-completa linha-completa-cert vazio">
            <div class="col-nome col-nome-cert"></div>

            <div class="col-check col-check-cert">
              <img src="/certificados-poo/arquivos/img/relogio.png" class="icone-relogio-emit">
              <div class="linha-carga linha-carga-cert"></div>
            </div>
          </div>
        <?php endfor; ?>

      <?php endif; ?>

    </div>

    <!-- BOTÃO LIMPAR -->
    <div class="emitir-footer-cert">
      <form action="?url=certificados/limpar"
            method="POST"
            onsubmit="return confirm('Tem certeza que deseja apagar todos os certificados?');">
        <button type="submit" class="btn-limpar">
          Limpar
        </button>
      </form>
    </div>

  </div>
</div>
