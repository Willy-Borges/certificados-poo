<?php
// /views/layouts/principal.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Certificados</title>
    <link rel="stylesheet" href="/certificados-poo/arquivos/css/style.css">
</head>

<body>

<div class="topo">

<?php

$img_titulo = '/certificados-poo/arquivos/img/sistema-de-certificados.png';
$classe_img = 'img-titulo';

if ($pagina === 'cursos/lista.php') {
    $img_titulo = '/certificados-poo/arquivos/img/cursos.png';
    $classe_img = 'img-titulo img-titulo-cursos';
} elseif ($pagina === 'emitir/formulario.php') {
    $img_titulo = '/certificados-poo/arquivos/img/emitir-certificado.png';
} elseif ($pagina === 'certificados/lista.php') {
    $img_titulo = '/certificados-poo/arquivos/img/certificados-emitidos.png';
    $classe_img = 'img-titulo img-titulo-certificados-emitidos';
}

?>

<img src="<?= $img_titulo ?>" class="<?= $classe_img ?>" alt="Título da Página">

<?php if ($pagina !== 'home/index.php'): ?>
    <nav class="menu-horizontal">
        <a href="?url=home">Home</a>
        <a href="?url=cursos">Cursos</a>
        <a href="?url=emitir">Emitir Certificado</a>
        <a href="?url=certificados">Certificados Emitidos</a>
    </nav>
<?php endif; ?>

</div>

<!-- >>> NÃO ALTERADO — apenas mantido exatamente como estava <<< -->
<div class="conteudo-principal">
    <?php
    $caminho = __DIR__ . '/../' . $pagina;

    if (file_exists($caminho)) {
        require $caminho;
    } else {
        echo "<p>Página não encontrada: $caminho</p>";
    }
    ?>
</div>

<script>
document.addEventListener("mousemove", function(e) {
    document.documentElement.style.setProperty("--mouse-x", e.clientX + "px");
    document.documentElement.style.setProperty("--mouse-y", e.clientY + "px");
});
</script>

<script src="/certificados-poo/arquivos/js/script.js"></script>

</body>
</html>
