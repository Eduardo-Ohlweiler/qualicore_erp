<?php

use Adianti\Database\TRecord;

class Telefone extends TRecord
{
    const TABLENAME  = 'telefone';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $id;
    private $pessoa_id;
    private $user_id;
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

    public function get_numero() 
    { 
        return $this->numero; 
    }
    public function set_numero($value) 
    { 
        $this->numero = $value; 
    }
}
