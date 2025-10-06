<?php

use Adianti\Database\TRecord;

class TipoEndereco extends TRecord
{
    const TABLENAME  = 'tipo_endereco';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }

}
