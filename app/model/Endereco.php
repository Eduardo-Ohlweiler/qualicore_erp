<?php

use Adianti\Database\TRecord;

class Endereco extends TRecord
{
    const TABLENAME  = 'endereco';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $id;
    private $pessoa_id;
    private $user_id;
    private $cep;
    private $cidade;
    private $estado;
    private $rua;
    private $bairro;
    private $complemento;
    private $numero;

    public function get_id() 
    { 
        return $this->id; 
    }
    public function set_id($value) 
    { 
        $this->id = $value; 
    }

    public function get_pessoa_id() 
    { 
        return $this->pessoa_id; 
    }
    public function set_pessoa_id($value) 
    { 
        $this->pessoa_id = $value; 
    }

    public function get_user_id() 
    { 
        return $this->user_id; 
    }
    public function set_user_id($value) 
    { 
        $this->user_id = $value; 
    }

    public function get_cep() 
    { 
        return $this->cep; 
    }
    public function set_cep($value) 
    { 
        $this->cep = $value; 
    }

    public function get_cidade() 
    { 
        return $this->cidade; 
    }
    public function set_cidade($value) 
    { 
        $this->cidade = $value; 
    }

    public function get_estado() 
    { 
        return $this->estado; 
    }
    public function set_estado($value) 
    { 
        $this->estado = $value; 
    }

    public function get_rua() 
    { 
        return $this->rua; 
    }
    public function set_rua($value) 
    { 
        $this->rua = $value; 
    }

    public function get_bairro() 
    { 
        return $this->bairro; 
    }
    public function set_bairro($value) 
    { 
        $this->bairro = $value; 
    }

    public function get_complemento() 
    { 
        return $this->complemento; 
    }
    public function set_complemento($value) 
    { 
        $this->complemento = $value; 
    }

    public function get_numero() 
    { 
        return $this->numero; 
    }
    public function set_numero($value) 
    { 
        $this->numero = $value; 
    }
}
