<?php

use Adianti\Database\TRecord;

class TipoPessoa extends TRecord
{
    const TABLENAME  = 'tipo_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }

}
