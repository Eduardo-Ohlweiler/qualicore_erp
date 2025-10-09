<?php

class FuncoesCalculos 
{
    public static function calcularMargensRetFloat($total, $valor, $decimais = 2): float
    {
        if ($total <= 0 || $valor < 0) {
            return 0.0;
        }

        return round(($valor / $total) * 100, $decimais);
    }
}