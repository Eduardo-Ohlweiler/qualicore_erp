<?php

use Adianti\Database\TRecord;

class TipoTelefone extends TRecord
{
    const TABLENAME  = 'tipo_telefone';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('user_id');
    }

}
