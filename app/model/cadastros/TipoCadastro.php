<?php

use Adianti\Database\TRecord;

class TipoCadastro extends TRecord
{
    const TABLENAME  = 'tipo_cadastro';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }

}
