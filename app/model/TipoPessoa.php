<?php

use Adianti\Database\TRecord;

class TipoPessoa extends TRecord
{
    const TABLENAME  = 'tipo_pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial'; // auto-increment

    private $id;
    private $nome;

    public function get_id() 
    { 
        return $this->id; 
    }
    public function set_id($value)
    { 
        $this->id = $value; 
    }

    public function get_nome() 
    { 
        return $this->nome; 
    }
    public function set_nome($value) 
    { 
        $this->nome = $value; 
    }
}
