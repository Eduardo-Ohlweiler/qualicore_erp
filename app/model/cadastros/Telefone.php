<?php

use Adianti\Database\TRecord;

class Telefone extends TRecord
{
    const TABLENAME  = 'telefone';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('tipo_telefone_id');
        parent::addAttribute('numero');
        parent::addAttribute('principal');//1 ou 2
    }

}
