<?php

use Adianti\Database\TRecord;
use Adianti\Registry\TSession;

class Maquina extends TRecord
{
    const TABLENAME  = 'maquina';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $tipo_cadastro;
    private $tipo_pessoa;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('bloqueado');
    }


    public function get_bloqueado_icon()
    {
        $ret = '';
        if((int)$this->bloqueado == 1)
            $ret = '<i class="fa fa-ban red"></i>';

        return $ret;
    }
}
