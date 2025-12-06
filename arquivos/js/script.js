/* js/script.js*/
document.addEventListener("DOMContentLoaded", () => {

  const checkSelector = ".curso-check";
  const hiddenOrdem = document.querySelector('input[name="ordem_selecionados"]');

  function getBoxes() {
    return Array.from(document.querySelectorAll(checkSelector));
  }

  function atualizarNumerosEBackend() {
    const boxes = getBoxes();

    boxes.forEach(b => {
      const fake = b.parentElement.querySelector(".fake-check");
      if (fake) fake.textContent = "";
    });

    const selecionados = boxes
      .filter(b => b.checked)
      .map(b => {
        const id = b.value;
        const ts = b.dataset.clicked ? Number(b.dataset.clicked) : Date.now();
        return { box: b, id, ts };
      })
      .sort((a, b) => a.ts - b.ts);

    selecionados.forEach((item, i) => {
      const fake = item.box.parentElement.querySelector(".fake-check");
      if (fake) fake.textContent = String(i + 1);
    });

    if (hiddenOrdem) {
      hiddenOrdem.value = selecionados.map(it => it.id).join(",");
    }
  }

  function bindListeners() {
    getBoxes().forEach(box => {
      box.addEventListener("click", onClickRecord);
      box.addEventListener("change", onChange);
    });
  }

  function onClickRecord() {
    if (!this.dataset.clicked) {
      this.dataset.clicked = Date.now();
    }
  }

  function onChange() {
    if (this.checked && !this.dataset.clicked) {
      this.dataset.clicked = Date.now();
    }
    if (!this.checked) {
      delete this.dataset.clicked;
    }
    atualizarNumerosEBackend();
  }

  bindListeners();
  atualizarNumerosEBackend();
});


function abrirPopup(msg, callback = null) {
  document.getElementById("popupTexto").textContent = msg;
  document.getElementById("popup").style.display = "flex";
  window._fecharCallback = callback;
}

function fecharPopup() {
  document.getElementById("popup").style.display = "none";

  if (typeof window._fecharCallback === "function") {
    window._fecharCallback();
  }

  window._fecharCallback = null;
}


document.addEventListener("DOMContentLoaded", () => {

  const botaoEmitir = document.getElementById("btnEmitir");
  const form = document.querySelector(".emitir-form");

  botaoEmitir.addEventListener("click", function (e) {

    const selecionados = document.querySelectorAll(".curso-check:checked");
    const qtd = selecionados.length;

    if (qtd === 0) {
      e.preventDefault();
      abrirPopup("Nenhum certificado a ser emitido.");
      return;
    }

    let msg = qtd === 1
      ? "Certificado emitido com sucesso!"
      : "Certificados emitidos com sucesso!";

    e.preventDefault();
    abrirPopup(msg, () => {
      form.submit();
    });

  });

});
