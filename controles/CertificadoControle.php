<?php
// controles/CertificadoControle.php

require_once __DIR__ . '/../modelos/Certificado.php';
require_once __DIR__ . '/../modelos/Curso.php';
require_once __DIR__ . '/../app/CalculoCertificado.php';
require_once __DIR__ . '/../app/Sessao.php';

class CertificadoControle
{
    private function escreverLog($msg)
    {
        @file_put_contents('/tmp/certificados_debug.log', date('Y-m-d H:i:s') . " - " . $msg . PHP_EOL, FILE_APPEND);
    }

    // LISTA
    public function lista()
    {
        $cert = new Certificado();
        $certificados = $cert->todos();

        $pagina = "certificados/lista.php";
        require __DIR__ . '/../views/layouts/principal.php';
    }

    // FORMULÁRIO
    public function formulario()
    {
        $cursoModel = new Curso();
        $cursos = $cursoModel->todos();

        $pagina = "emitir/formulario.php";
        require __DIR__ . '/../views/layouts/principal.php';
    }

    // SALVAR
    public function salvar()
    {
        Sessao::iniciar();

        $this->escreverLog('salvar() chamado. POST: ' . json_encode($_POST));

        $cursoIds = [];

        if (!empty($_POST['curso_selecionado']) && is_array($_POST['curso_selecionado'])) {
            $cursoIds = array_map('intval', $_POST['curso_selecionado']);
        } elseif (!empty($_POST['id_curso'])) {
            $cursoIds[] = (int) $_POST['id_curso'];
        }

        if (empty($cursoIds)) {
            $this->escreverLog('Nenhum curso selecionado (vazio). Redirecionando para emitir.');
            header('Location: ?url=emitir');
            exit;
        }

        $ordemCsv = $_POST['ordem_selecionados'] ?? '';
        $ordem = [];
        if (!empty($ordemCsv)) {
            $ordem = array_filter(array_map('intval', explode(',', $ordemCsv)));
        }

        $cursoModel = new Curso();
        $cursosSelecionados = [];
        foreach ($cursoIds as $id) {
            $c = $cursoModel->buscarPorId($id);
            if ($c) $cursosSelecionados[] = $c;
            else $this->escreverLog("buscarPorId retornou null para id: {$id}");
        }

        if (empty($cursosSelecionados)) {
            Sessao::definir('erro_emissao', 'Nenhum curso válido selecionado.');
            header('Location: ?url=emitir');
            exit;
        }

        try {
            $calc = new CalculoCertificado();
            $resultado = $calc->calcular($cursosSelecionados, $ordem);
        } catch (Throwable $e) {
            Sessao::definir('erro_emissao', 'Erro ao calcular carga horária.');
            header('Location: ?url=emitir');
            exit;
        }

        if (!empty($resultado['erro']) && empty($resultado['certificados'])) {
            Sessao::definir('erro_emissao', $resultado['erro']);
            header('Location: ?url=emitir');
            exit;
        }

        if (empty($resultado['certificados'])) {
            Sessao::definir('erro_emissao', 'Nenhum certificado foi gerado.');
            header('Location: ?url=emitir');
            exit;
        }

        $certModel = new Certificado();

        try {
            foreach ($resultado['certificados'] as $item) {

                if (empty($item['curso_id'])) continue;

                $carga = $item['horas'] ?? ($item['carga'] ?? null);
                if (empty($carga)) {
                    if (!empty($item['horas_float'])) {
                        $hh = floor($item['horas_float']);
                        $mm = floor(($item['horas_float'] - $hh) * 60);
                        $carga = sprintf('%02d:%02d:00', $hh, $mm);
                    } else {
                        continue;
                    }
                }

                $certModel->salvarVarios([(int)$item['curso_id']], $carga);
            }
        } catch (Throwable $e) {
            Sessao::definir('erro_emissao', 'Erro ao salvar certificados.');
            header('Location: ?url=emitir');
            exit;
        }

        Sessao::definir('sucesso_emissao', 'Certificados emitidos com sucesso.');
        header('Location: ?url=certificados');
        exit;
    }

    // LIMPAR
    public function limpar()
    {
        $cert = new Certificado();
        $cert->limpar();
        header('Location: ?url=certificados');
        exit;
    }

    

    public function visualizar()
{
    // id
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        // Mensagem simples — não estamos gerando PNG
        echo "ID inválido";
        return;
    }

    // buscar certificado
    $certModel = new Certificado();
    $c = $certModel->buscarPorIdEmitido($id);
    if (!$c) {
        echo "Certificado não encontrado";
        return;
    }

    // usuário
    require_once __DIR__ . '/../modelos/Usuario.php';
    $usuarioModel = new Usuario();
    $aluno = $usuarioModel->buscarPorId($c['usuario_id']);

    $nomeAluno = $aluno['nome'] ?? 'Aluno';
    $cursoNome = $c['curso_nome'] ?? $c['nome'];
    $carga = $c['carga'] ?? $c['carga_horaria'] ?? '';
    $inicio = $c['curso_inicio'] ?? '';
    $fim = $c['curso_fim'] ?? '';

    $serial = $this->gerarSerial();

    // Caminho real do template (baseado na estrutura do projeto)
    $raiz = dirname(__DIR__); // sobe para a raiz do projeto
    $template = $raiz . '/arquivos/img/modelo_certificado.png';

    if (!file_exists($template)) {
        // só para desenvolvimento; em produção remover
        echo "Template NÃO encontrado → $template";
        return;
    }

    // --- Preparar ambiente para enviar um PNG puro ---
    // Limpa buffers que possam existir (evita texto antes do binário)
    while (ob_get_level()) { ob_end_clean(); }

    // Desativa display_errors temporariamente para não enviar warnings para o browser
    $prevDisplayErrors = ini_get('display_errors');
    ini_set('display_errors', '0');

    // Carrega a imagem (suprime warnings do GD com @)
    $img = @imagecreatefrompng($template);
    if (!$img) {
        // restaura display_errors e informa erro em modo texto
        ini_set('display_errors', $prevDisplayErrors);
        echo "Falha ao abrir template com GD";
        return;
    }

    // aloca cor e fonte
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);

    // fonte TTF (usar Arial como fallback)
    $fontTtf = $raiz . '/arquivos/fonts/Roboto-Regular.ttf';
    if (!file_exists($fontTtf)) {
        $fontTtf = $raiz . '/arquivos/fonts/arial.ttf';
    }

    // se a fonte não existir, ainda assim podemos continuar (imagettftext precisa da fonte)
    if (!file_exists($fontTtf)) {
        // restaura display_errors
        ini_set('display_errors', $prevDisplayErrors);
        imagedestroy($img);
        echo "Fonte TTF não encontrada: " . $raiz . '/arquivos/fonts/';
        return;
    }

    $inicioFix = str_replace('/', '-', $inicio);
    $fimFix    = str_replace('/', '-', $fim);

    $dataInicio = date('d/m/Y', strtotime($inicioFix));
    $dataFim    = date('d/m/Y', strtotime($fimFix));


    // escrevendo textos no certificado
    imagettftext($img, 110, 0, 400, 960, $white, $fontTtf, $nomeAluno);
    imagettftext($img, 80, 0, 1760, 1200, $white, $fontTtf, $cursoNome);
    imagettftext($img, 80, 0, 1620, 1331, $white, $fontTtf, $carga . 'h');
    imagettftext($img, 80, 0, 280, 1465, $white, $fontTtf, $dataInicio);
    imagettftext($img, 80, 0, 1000, 1465, $white, $fontTtf, $dataFim);
    imagettftext($img, 32, 0, 2900, 2200, $black, $fontTtf, "Código: $serial");

    // Envia cabeçalho e imagem binária
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);

    ini_set('display_errors', $prevDisplayErrors);

    exit;
}


    private function gerarSerial()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
        return
            $letters[random_int(0, 25)] .
            $digits[random_int(0, 9)] .
            $letters[random_int(0, 25)] .
            $digits[random_int(0, 9)] .
            $digits[random_int(0, 9)];
    }

   
    public function gerarPng()
    {
        return $this->visualizar();
    }
}
