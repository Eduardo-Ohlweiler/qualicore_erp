<?php

use Adianti\Database\TRecord;

class ConferenciaUsinagemDetalhamento extends TRecord
{
    const TABLENAME  = 'conferencia_usinagem_detalhamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $conferencia_usinagem;
    private $maquina;
    private $pessoa;
    private $turno;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('conferencia_usinagem_id');
        parent::addAttribute('data');
        parent::addAttribute('retrabalho');
        parent::addAttribute('maquina_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('turno_id');
        parent::addAttribute('quantidade_retrabalho');
        parent::addAttribute('quantidade_refugo');
        parent::addAttribute('obs');
    }

    public function get_conferencia_usinagem()
    {
        if (empty($this->conferencia_usinagem))
            $this->conferencia_usinagem = new ConferenciaUsinagem($this->conferencia_usinagem_id);
        return $this->conferencia_usinagem;
    }

    public function get_maquina()
    {
        if (empty($this->maquina))
            $this->maquina = new Maquina($this->maquina_id);
        return $this->maquina;
    }

    public function get_pessoa()
    {
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
        return $this->pessoa;
    }

    public function get_turno()
    {
        if (empty($this->turno))
            $this->turno = new Turno($this->turno_id);
        return $this->turno;
    }
}
