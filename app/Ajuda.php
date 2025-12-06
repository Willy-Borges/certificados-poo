<?php
// app/Ajuda.php

class Ajuda
{
    /* ===== datas por dia ===== */
    public static function datasPorDia($inicio, $fim)
    {
        $dias = [];
        $dtIni = new DateTime($inicio);
        $dtFim = new DateTime($fim);

        $iniDia = (new DateTime($inicio))->format('Y-m-d');
        $fimDia = (new DateTime($fim))->format('Y-m-d');

        while ($dtIni <= $dtFim) {
            $dia = $dtIni->format('Y-m-d');

            if (!isset($dias[$dia])) {
                $dias[$dia] = [
                    'dia' => $dia,
                    'inicio' => null,
                    'fim' => null
                ];
            }

            if ($dia === $iniDia) {
                $dias[$dia]['inicio'] = $inicio;
            }
            if ($dia === $fimDia) {
                $dias[$dia]['fim'] = $fim;
            }

            $dtIni->modify('+1 day');
        }

        return $dias;
    }

    /* ===== horas por dia ===== */
    public static function horasDia($dia, $inicio, $fim)
    {
        $ini = $inicio ? $inicio : $dia . ' 00:00:00';
        $f   = $fim    ? $fim    : $dia . ' 23:59:59';

        $dtInicio = new DateTime($ini);
        $dtFim    = new DateTime($f);

        if ($dtFim <= $dtInicio) {
            return 0;
        }

        $diff = $dtFim->getTimestamp() - $dtInicio->getTimestamp();
        $horas = $diff / 3600;

        return ($horas > 10) ? 10 : $horas;
    }

    /* ===== formatar hh:mm:ss ===== */
    public static function formatarHoras($h)
    {
        $hTotal = floor($h);
        $m = floor(($h - $hTotal) * 60);

        return sprintf('%02d:%02d:00', $hTotal, $m);
    }

    /* ===== somar horas ===== */
    public static function somarHoras($a, $b)
    {
        list($h1, $m1) = explode(':', $a);
        list($h2, $m2) = explode(':', $b);

        $totalMin = $m1 + $m2;
        $hora = $h1 + $h2 + floor($totalMin / 60);
        $min  = $totalMin % 60;

        return sprintf('%02d:%02d:00', $hora, $min);
    }
}
