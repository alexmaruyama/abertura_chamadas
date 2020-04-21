<?php

use Adianti\Database\TRecord;

class Anotacao extends TRecord
{
    const TABLENAME = 'anotacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    const CREATEAT = 'data_anotacao';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('data_anotacao');
        parent::addAttribute('descricao');
        parent::addAttribute('ocorrencia');
    }
}
