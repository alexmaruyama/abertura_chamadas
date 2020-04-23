<?php

use Adianti\Database\TRecord;

class Ocorrencia extends TRecord
{
    const TABLENAME = 'ocorrencia';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('data_abertura');
        parent::addAttribute('data_fechamento');
        parent::addAttribute('ramal');
        parent::addAttribute('descricao');
        parent::addAttribute('problema_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('flag');
        parent::addAttribute('descricao_acompanhamento');
    }

    public function get_system_user()
    {
        return SystemUser::find($this->system_user_id);
    }

    public function get_problema()
    {
        return Problema::find($this->problema_id);
    }
}
