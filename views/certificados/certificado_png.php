<?php
// views/certificados/certificado_png.php

if (ob_get_level()) {
    ob_end_clean();
}

require_once __DIR__ . '/../../modelos/Certificado.php';
require_once __DIR__ . '/../../modelos/Usuario.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    exit('ID inválido');
}

$certModel = new Certificado();
$c = $certModel->buscarPorIdEmitido($id);

if (!$c) {
    exit('Certificado não encontrado.');
}

$usuarioModel = new Usuario();
$aluno = $usuarioModel->buscarPorId($c['usuario_id']);

$nomeAluno = $aluno['nome'] ?? 'Aluno';
$cursoNome = $c['curso_nome'] ?? $c['nome'] ?? '';
$carga = $c['carga'] ?? $c['carga_horaria'] ?? '';
$inicio = $c['curso_inicio'] ?? '';
$fim = $c['curso_fim'] ?? '';

$serial = strtoupper(substr(bin2hex(random_bytes(4)), 0, 12));

// Caminho correto do template
$template = __DIR__ . '/../arquivos/img/modelo_certificado.png';

if (!file_exists($template)) {
    exit("Template não encontrado em: $template");
}

$img = imagecreatefrompng($template);
if (!$img) {
    exit('Falha ao carregar imagem base.');
}

$black = imagecolorallocate($img, 0, 0, 0);


$font = __DIR__ . '/../../arquivos/fonts/arial.ttf';


// TEXTOS NO CERTIFICADO
imagettftext($img, 42, 0, 300, 620, $black, $font, $nomeAluno);
imagettftext($img, 34, 0, 300, 740, $black, $font, $cursoNome);
imagettftext($img, 32, 0, 1300, 740, $black, $font, $carga . 'h');
imagettftext($img, 28, 0, 300, 860, $black, $font, "Início: $inicio");
imagettftext($img, 28, 0, 900, 860, $black, $font, "Fim: $fim");
imagettftext($img, 22, 0, 1450, 1110, $black, $font, "Serial: $serial");

// Saída PNG
header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);
exit;
