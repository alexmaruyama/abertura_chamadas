<?php

use Adianti\Base\AdiantiStandardFormTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class ProblemaForm extends TPage
{
    protected $form;

    use AdiantiStandardFormTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('banco');
        $this->setActiveRecord('Problema');

        $this->form = new BootstrapFormBuilder('form_problema');
        $this->form->setFormTitle('Cadastro problema');

        $id = new TEntry('id');
        $nome = new TEntry('nome');

        $id->setEditable(false);

        $id->setSize('25%');
        $nome->setSize('50%');

        $nome->addValidation('Nome', new TRequiredValidator);

        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Nome', 'red')], [$nome]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }
}
