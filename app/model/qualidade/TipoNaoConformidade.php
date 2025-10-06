<?php

use Adianti\Database\TRecord;

class TipoNaoConformidade extends TRecord
{
    const TABLENAME  = 'tipo_nao_conformidade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('bloqueado');
    }

}
