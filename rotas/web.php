<?php
// rotas/web.php

return [
    'home' => 'HomeControle@index',
    'cursos' => 'CursoControle@lista',
    'emitir' => 'CertificadoControle@formulario',
    'emitir-salvar' => 'CertificadoControle@salvar',
    'certificados' => 'CertificadoControle@lista',
    'certificados/limpar' => 'CertificadoControle@limpar',
    'certificado/visualizar' => 'CertificadoControle@visualizar',

    'certificado_png' => 'CertificadoControle@gerarPng'
];
