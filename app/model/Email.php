<?php

use Adianti\Database\TRecord;

class Email extends TRecord
{
    const TABLENAME  = 'email';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'serial';

    private $id;
    private $pessoa_id;
    private $user_id;
    private $email;

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

    public function get_email() 
    { 
        return $this->email; 
    }
    public function set_email($value) 
    { 
        $this->email = $value; 
    }
}
