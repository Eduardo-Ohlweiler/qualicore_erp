<?php

use Adianti\Database\TRecord;

class Endereco extends TRecord
{
    const TABLENAME  = 'endereco';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_endereco_id');
        parent::addAttribute('cidade_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('rua');
        parent::addAttribute('bairro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
    }

}
