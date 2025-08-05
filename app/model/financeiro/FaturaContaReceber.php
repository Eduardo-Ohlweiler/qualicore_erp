<?php

use Adianti\Database\TRecord;

class FaturaContaReceber extends TRecord
{
    const TABLENAME  = 'fatura_conta_receber';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('fatura_id');
        parent::addAttribute('conta_receber_id');        
    }

}
