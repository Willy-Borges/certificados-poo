<?php
// views/emitir/formulario.php
?>
<form action="?url=emitir-salvar" 
      method="POST" 
      class="emitir-form">

    <input type="hidden" name="ordem_selecionados" value="">

    <div class="emitir-columns">

        <div class="emitir-header">
            <div class="titulo-coluna">Cursos</div>
            <div class="titulo-coluna">Selecionar</div>
        </div>

        <div class="emitir-list">
            <?php foreach ($cursos as $curso): ?>
                <div class="linha-completa">
                    
                    <div class="col-nome">
                        <?= htmlspecialchars($curso['nome']); ?>
                    </div>

                    <div class="col-check">
                        <label class="check-wrapper">
                            <input type="checkbox"
                                   name="curso_selecionado[]"
                                   value="<?= (int)$curso['id']; ?>"
                                   class="curso-check">
                            <span class="fake-check"></span>
                        </label>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <div class="emitir-actions">
        
        <button type="submit" 
                class="btn-emitir" 
                id="btnEmitir" 
                title="Emitir">
                <img src="/certificados-poo/arquivos/img/botao-emitir.png" 
                 alt="Emitir">
        </button>

        <div class="emitir-msg" id="emitirMsg"></div>

    </div>

</form>
<div id="popup" class="popup-overlay">
  <div class="popup-box">
    <div id="popupTexto"></div>
    <button onclick="fecharPopup()">OK</button>
  </div>
</div>

