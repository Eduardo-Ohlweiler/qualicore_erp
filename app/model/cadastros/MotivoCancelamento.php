<?php

use Adianti\Database\TRecord;

class MotivoCancelamento extends TRecord
{
    const TABLENAME  = 'motivo_cancelamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('motivo');
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
