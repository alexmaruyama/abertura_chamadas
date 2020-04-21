<?php

use Adianti\Base\AdiantiStandardListTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ProblemaList extends TPage
{
    protected $form, $datagrid, $pageNavigation;

    use AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('banco');
        $this->setActiveRecord('Problema');
        $this->setLimit(5);
        $this->setDefaultOrder('id', 'desc');
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('nome', 'like', 'nome');

        $this->form = new BootstrapFormBuilder('form_problema_search');
        $this->form->setFormTitle('Cadastro problema');

        $id = new TEntry('id');
        $nome = new TEntry('nome');

        $row = $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Nome')], [$nome]);
        $row->layout = ['col-sm-1', 'col-sm-5', 'col-sm-1', 'col-sm-5'];

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');
        $this->form->addAction('Cadastrar', new TAction(['ProblemaForm', 'onEdit']), 'fa:plus blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_nome = new TDataGridColumn('nome', 'Nome', 'left');

        $col_id->setAction(new TAction([$this, 'onReload'], ['order' => 'id']));
        $col_nome->setAction(new TAction([$this, 'onReload'], ['order' => 'nome']));

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);

        $acao_editar = new TDataGridAction(['ProblemaForm', 'onEdit'], ['id' => '{id}']);
        $acao_excluir = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($acao_editar, 'Editar', 'fa:edit blue');
        $this->datagrid->addAction($acao_excluir, 'Excluir', 'fa:trash red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        $panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation);

        $vbox = TVBox::pack($this->form, $panel);
        $vbox->style = 'width:100%';

        parent::add($vbox);
    }
}
