<?php

use Adianti\Database\TRecord;

class Problema extends TRecord
{
    const TABLENAME = 'problema';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = null)
    {
        parent::__construct($id);

        parent::addAttribute('nome');
    }
}
