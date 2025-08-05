<?php

use Adianti\Database\TRecord;

class Fatura extends TRecord
{
    const TABLENAME  = 'fatura';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_pessoa_id');
        parent::addAttribute('dt_fatura');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('total');
        parent::addAttribute('financeiro_gerado');//1 ou 2
        parent::addAttribute('ativo');//1 ou 2
    }

}
