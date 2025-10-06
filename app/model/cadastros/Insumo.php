<?php

use Adianti\Database\TRecord;
use Adianti\Registry\TSession;

class Insumo extends TRecord
{
    const TABLENAME  = 'insumo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $tipo_insumo;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('codigo');
        parent::addAttribute('criado_em');
        parent::addAttribute('alterado_em');
        parent::addAttribute('criou_pessoa_id');
        parent::addAttribute('alterou_pessoa_id');
        parent::addAttribute('tipo_insumo_id');
        parent::addAttribute('bloqueado');
    }

    public function get_tipo_insumo()
    {
        if(empty($this->tipo_insumo))
            $this->tipo_insumo = new TipoInsumo($this->tipo_insumo_id);

        return $this->tipo_insumo;
    }

    public function get_bloqueado_icon()
    {
        $ret = '';
        if((int)$this->bloqueado == 1)
            $ret = '<i class="fa fa-ban red"></i>';

        return $ret;
    }
}
