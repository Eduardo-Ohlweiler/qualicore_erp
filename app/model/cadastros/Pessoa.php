<?php

use Adianti\Database\TRecord;
use Adianti\Registry\TSession;

class Pessoa extends TRecord
{
    const TABLENAME  = 'pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('altura');
        parent::addAttribute('cpf');
        parent::addAttribute('cnpj');
        parent::addAttribute('tipo_cadastro_id');
        parent::addAttribute('tipo_pessoa_id');
        parent::addAttribute('user_id');
        parent::addAttribute(attribute: 'bloqueado');
    }

    public function get_cpf_or_cnpj()
    {
        $ret = '';
        if(!empty($this->cpf))
            $ret = $this->cpf;
        else
            $ret = $this->cnpj;

        return $ret;
    }

    public function get_email_list_br()
    {
        $ret = '';
        $user = TSession::getValue('userid');
        $emails = Email::where('pessoa_id = '.$this->id.' and user_id', '=', $user)->load();
        if($emails)
        {
            foreach($emails as $email)
            {
                if(!empty($ret))
                    $ret .= "<br>";
                $ret .= $email->email;
            }
        }
        return $ret;
    }

    public function get_telefone_list_br()
    {
        $ret = '';
        $user = TSession::getValue('userid');
        $telefones = Telefone::where('pessoa_id = '.$this->id.' and user_id', '=', $user)->load();
        if($telefones)
        {
            foreach($telefones as $telefone)
            {
                if(!empty($ret))
                    $ret .= "<br>";
                $ret .= $telefone->numero;
            }
        }
        return $ret;
    }

    public function get_bloqueado_icon()
    {
        $ret = '';
        if((int)$this->bloqueado == 1)
            $ret = '<i class="fa fa-ban red"></i>';

        return $ret;
    }
}
