<?php
header("Content-Type: image/png");

$path = __DIR__ . '/../arquivos/img/modelo_certificado.png';
$img = imagecreatefrompng($path);

imagepng($img);
imagedestroy($img);
