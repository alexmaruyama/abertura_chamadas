<?php

use Adianti\Database\TRecord;

class Anexo extends TRecord
{
    const TABLENAME = 'anexo';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('caminho_anexo');
        parent::addAttribute('ocorrencia_id');
    }
}
