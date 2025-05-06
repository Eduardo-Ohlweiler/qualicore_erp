<?php

use Adianti\Database\TRecord;

class Pessoa extends TRecord
{
    const TABLENAME  = 'pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $id;
    private $user_id;
    private $tipo_pessoa_id;
    private $nome;
    private $data_nascimento;

    public function get_id() 
    { 
        return $this->id; 
    }
    public function set_id($value) 
    { 
        $this->id = $value; 
    }

    public function get_user_id() 
    { 
        return $this->user_id; 
    }
    public function set_user_id($value)
    {
         $this->user_id = $value; 
        }

    public function get_tipo_pessoa_id() 
    { 
        return $this->tipo_pessoa_id; 
    }
    public function set_tipo_pessoa_id($value) 
    { 
        $this->tipo_pessoa_id = $value; 
    }

    public function get_nome() 
    { 
        return $this->nome; }
    public function set_nome($value) 
    { 
        $this->nome = $value; 
    }

    public function get_data_nascimento() 
    { 
        return $this->data_nascimento; 
    }
    public function set_data_nascimento($value) 
    { 
        $this->data_nascimento = $value; 
    }
}
