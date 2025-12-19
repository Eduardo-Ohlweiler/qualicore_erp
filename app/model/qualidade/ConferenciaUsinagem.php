<?php

use Adianti\Database\TRecord;

class ConferenciaUsinagem extends TRecord
{
    const TABLENAME  = 'conferencia_usinagem';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $insumo;
    private $criado_por_usuario;
    private $alterado_por_usuario;
    private $motivo_cancelamento;
    private $conferencia_usinagem_detalhamento;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('ordem_servico');
        parent::addAttribute('insumo_id');
        parent::addAttribute('quantidade_total');
        parent::addAttribute('quantidade_refugo');
        parent::addAttribute('quantidade_retrabalho');
        parent::addAttribute('criado_em');
        parent::addAttribute('alterado_em');
        parent::addAttribute('criado_por');
        parent::addAttribute('alterado_por');
        parent::addAttribute('cancelado');
        parent::addAttribute('motivo_cancelamento_id');
    }

    public function get_insumo()
    {
        if (empty($this->insumo))
            $this->insumo = new Insumo($this->insumo_id);
        return $this->insumo;
    }

    public function get_insumo_descricao_id_cod_desenho()
    {
        return $this->get_insumo_descricao().' ('.$this->insumo_id.') ('.$this->get_insumo()->codigo.')';
    }

    public function get_insumo_descricao()
    {
        return $this->get_insumo()->descricao;
    }

    public function get_criado_por_usuario()
    {
        if (empty($this->criado_por_usuario))
            $this->criado_por_usuario = new SystemUser($this->criado_por);
        return $this->criado_por_usuario;
    }

    public function get_alterado_por_usuario()
    {
        if (empty($this->alterado_por_usuario))
            $this->alterado_por_usuario = new SystemUser($this->alterado_por);
        return $this->alterado_por_usuario;
    }

    public function get_motivo_cancelamento()
    {
        if (empty($this->motivo_cancelamento))
            $this->motivo_cancelamento = new MotivoCancelamento($this->motivo_cancelamento_id);
        return $this->motivo_cancelamento;
    }

    public function get_conferencia_usinagem_detalhamento()
    {
        $ret = '';
        $conferencia_usinagem_detalhamento = ConferenciaUsinagemDetalhamento::where('conferencia_usinagem_id', '=', $this->id)->load();
        if(!empty($conferencia_usinagem_detalhamento))
            $ret = $conferencia_usinagem_detalhamento;

        return $ret;
    }
}
