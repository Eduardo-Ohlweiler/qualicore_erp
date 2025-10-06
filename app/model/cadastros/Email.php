<?php

use Adianti\Database\TRecord;

class Email extends TRecord
{
    const TABLENAME  = 'email';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('tipo_email_id');
        parent::addAttribute('email');
        parent::addAttribute('principal');//1 ou 2
    }

}
