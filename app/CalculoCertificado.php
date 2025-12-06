<?php
// app/CalculoCertificado.php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/Ajuda.php';

class CalculoCertificado
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    private function dividirPorDias($inicio, $fim)
    {
        $dias = [];
        $atual = strtotime(date("Y-m-d 00:00:00", strtotime($inicio)));
        $ultimo = strtotime($fim);

        while ($atual <= $ultimo) {
            $dia = date("Y-m-d", $atual);

            $inicioDia = max(strtotime("$dia 00:00:00"), strtotime($inicio));
            $fimDia    = min(strtotime("$dia 23:59:59"), strtotime($fim));

            if ($inicioDia < $fimDia) {
                $duracao = ($fimDia - $inicioDia) / 3600;
                $dias[$dia] = $duracao;
            }

            $atual = strtotime("+1 day", $atual);
        }

        return $dias;
    }

    private function limitar10h(array $dias)
    {
        foreach ($dias as $d => $h) {
            if ($h > 10) $dias[$d] = 10;
        }
        return $dias;
    }

    private function buscarCertificadosExistentes()
    {
        $sql = "SELECT ce.carga_horaria, c.data_inicio AS curso_inicio, c.data_fim AS curso_fim
                FROM certificados_emitidos ce
                LEFT JOIN cursos c ON c.id = ce.curso_id
                WHERE ce.carga_horaria IS NOT NULL AND ce.carga_horaria <> ''";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // distribui carga_horaria de um certificado existente ao longo dos dias do curso
    private function distribuirCargaPorDiasDeCurso($cargaHorasFloat, $inicio, $fim)
    {
        $dias = $this->dividirPorDias($inicio, $fim);
        $dias = $this->limitar10h($dias);

        $totalRaw = array_sum($dias);
        $resultado = [];
        if ($totalRaw <= 0) return $resultado;

        foreach ($dias as $dia => $dur) {
            // proporção do dia sobre o total raw
            $share = ($dur / $totalRaw) * $cargaHorasFloat;
            $resultado[$dia] = $share;
        }

        return $resultado;
    }

    // converter HH:MM:SS para horas float
    private function hhmmssParaHorasFloat($hhmmss)
    {
        if (empty($hhmmss)) return 0.0;
        $parts = explode(':', $hhmmss);
        $h = (int)($parts[0] ?? 0);
        $m = (int)($parts[1] ?? 0);
        $s = (int)($parts[2] ?? 0);
        return $h + ($m / 60) + ($s / 3600);
    }

    // converter horas float para HH:MM:00 (truncando minutos)
    private function horasFloatParaHHMMSS($h)
    {
        return Ajuda::formatarHoras($h);
    }

    // função pública: calcular
    // $cursos: array de cursos, cada item precisa ter id, nome, inicio, fim
    // $ordem: array com ids na ordem de prioridade (clique). Se vazio, usa ordem de $cursos
    public function calcular(array $cursos, array $ordem = [])
    {
        $resposta = [
            'erro' => null,
            'certificados' => []
        ];

        if (empty($cursos)) {
            $resposta['erro'] = 'Nenhum curso informado.';
            return $resposta;
        }

        // se não veio ordem, usar a ordem dos cursos informados
        if (empty($ordem)) {
            $ordem = array_map(fn($c)=> (int)$c['id'], $cursos);
        }

        // 1) construir mapa de dias disponíveis inicialmente (10h/dia)
        $diasDisponiveis = []; // dia => 10 (horas)
        $cursoDiasMax = [];   // cursoId => [ dia => maxHorasNesseDia ]

        foreach ($cursos as $c) {
            $cid = (int)$c['id'];
            $dias = $this->dividirPorDias($c['data_inicio'], $c['data_fim']);
            $dias = $this->limitar10h($dias);
            $cursoDiasMax[$cid] = $dias;
            foreach ($dias as $dia => $h) {
                if (!isset($diasDisponiveis[$dia])) $diasDisponiveis[$dia] = 10;
            }
        }

        // 2) descontar horas já consumidas por certificados existentes
        $certExist = $this->buscarCertificadosExistentes();
        foreach ($certExist as $ce) {
            $cargaStr = $ce['carga_horaria'] ?? '';
            $cargaFloat = $this->hhmmssParaHorasFloat($cargaStr);
            if ($cargaFloat <= 0) continue;

            $inicio = $ce['curso_inicio'] ?? null;
            $fim = $ce['curso_fim'] ?? null;
            if (!$inicio || !$fim) continue;

            $diaDistribuido = $this->distribuirCargaPorDiasDeCurso($cargaFloat, $inicio, $fim);
            foreach ($diaDistribuido as $dia => $consumo) {
                if (!isset($diasDisponiveis[$dia])) {
                    // pode ser dia fora do período atual; apenas inicializa negativo para registrar consumo
                    $diasDisponiveis[$dia] = max(0, 10 - $consumo);
                } else {
                    $diasDisponiveis[$dia] = max(0, $diasDisponiveis[$dia] - $consumo);
                }
            }
        }

        // 3) agora distribuir os diasDisponiveis entre os cursos solicitados seguindo a $ordem
        $resultadoHoras = []; // cursoId => horas(float)
        foreach ($cursos as $c) $resultadoHoras[(int)$c['id']] = 0.0;

        // construir mapa dias -> lista de cursos que têm presença nesse dia
        $diasPeriodo = []; // dia => [cursoId => maxHorasNoDia]
        foreach ($cursoDiasMax as $cid => $dias) {
            foreach ($dias as $dia => $h) {
                if (!isset($diasPeriodo[$dia])) $diasPeriodo[$dia] = [];
                $diasPeriodo[$dia][$cid] = $h;
            }
        }

        // iterar por cada dia e distribuir horas seguindo a ordem
        foreach ($diasPeriodo as $dia => $listaCursosDia) {
            $totalDiaDisponivel = $diasDisponiveis[$dia] ?? 10;
            if ($totalDiaDisponivel <= 0) continue;

            // selecionar apenas cursos do pedido que existem nesse dia, preservando ordem
            $ordenados = array_filter($ordem, fn($id) => isset($listaCursosDia[$id]));

            foreach ($ordenados as $cid) {
                if ($totalDiaDisponivel <= 0) break;
                $maximoCursoDia = $listaCursosDia[$cid];
                $consumo = min($maximoCursoDia, $totalDiaDisponivel);
                $resultadoHoras[$cid] += $consumo;
                $totalDiaDisponivel -= $consumo;
            }
        }

        // 4) preparar saída, aplicar mínimo 2h
        $certificadosParaSalvar = [];
        $erros = [];

        foreach ($resultadoHoras as $cid => $horasFloat) {
            // arredondar para minutos (arredondamento floor dos minutos)
            $hh = floor($horasFloat);
            $mm = floor(($horasFloat - $hh) * 60);
            $horasFloatArred = $hh + ($mm / 60);

            if ($horasFloatArred < 2.0) {
                $erros[] = "Curso ID {$cid} possui apenas " . $this->horasFloatParaHHMMSS($horasFloatArred) . " (mínimo 02:00:00).";
                continue;
            }

            $certificadosParaSalvar[] = [
                'curso_id' => $cid,
                'horas_float' => $horasFloatArred,
                'horas' => $this->horasFloatParaHHMMSS($horasFloatArred)
            ];
        }

        if (!empty($erros)) {
            $resposta['erro'] = implode(' ', $erros);
            // caso queiram salvar apenas os que são válidos, poderíamos retornar parcialmente;
            // porém, pelo seu enunciado, queremos avisar que alguns não podem ser emitidos.
            // retornamos o que é possível emitir também.
            $resposta['certificados'] = $certificadosParaSalvar;
            return $resposta;
        }

        $resposta['certificados'] = $certificadosParaSalvar;
        return $resposta;
    }
}
