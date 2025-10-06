<?php

use Adianti\Database\TRecord;
use Adianti\Registry\TSession;

class Pessoa extends TRecord
{
    const TABLENAME  = 'pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $tipo_cadastro;
    private $tipo_pessoa;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('criado_em');
        parent::addAttribute('alterado_em');
        parent::addAttribute('criou_pessoa_id');
        parent::addAttribute('alterou_pessoa_id');
        parent::addAttribute('bloqueado');
    }

    public function get_nome_id()
{
    return "{$this->nome} ({$this->id})";
}


    public function get_bloqueado_icon()
    {
        $ret = '';
        if((int)$this->bloqueado == 1)
            $ret = '<i class="fa fa-ban red"></i>';

        return $ret;
    }
}
