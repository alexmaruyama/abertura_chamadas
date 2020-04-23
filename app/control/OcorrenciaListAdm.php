<?php

use Adianti\Base\AdiantiStandardListTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanel;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TScroll;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class OcorrenciaListAdm extends TPage
{
    protected $form, $datagrid, $pageNavigation;

    use AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('banco');
        $this->setActiveRecord('Ocorrencia');
        $this->setLimit(5);
        $this->setDefaultOrder('id', 'desc');
        $this->addFilterField('id', '=', 'id');

        $this->form = new BootstrapFormBuilder('form_ocorrencia');
        $this->form->setFormTitle('Ocorrências');

        $id = new TEntry('id');
        $id->setSize('25%');

        $this->form->addFields([new TLabel('ID')], [$id]);

        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $this->form->addAction('Procurar', new TAction([$this, 'onSearch']), 'fa:search green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%;overflow-x:auto';

        $col_id = new TDataGridColumn('id', 'ID', 'left');
        $col_data_abertura = new TDataGridColumn('data_abertura', 'Data abertura', 'left');
        $col_data_fechamento = new TDataGridColumn('data_fechamento', 'Data fechamento', 'left');
        $col_system_user_id = new TDataGridColumn('{system_user->name}', 'Usuário', 'left');
        $col_ramal = new TDataGridColumn('ramal', 'Ramal', 'left');
        $col_problema_id = new TDataGridColumn('{problema->nome}', 'Problema', 'left');
        $col_flag = new TDataGridColumn('flag', 'Status', 'left');

        $formata_data = function ($data) {
            return TDateTime::convertToMask($data, 'yyyy-mm-dd hh:ii', 'dd/mm/yyyy hh:ii');
        };

        $col_data_abertura->setTransformer($formata_data);
        $col_data_fechamento->setTransformer($formata_data);

        $col_flag->setTransformer(function ($flag) {
            $status = ['ABERTO', 'ATENDIMENTO', 'FECHADO'];
            $cor = ['red', 'yellow', 'green'];
            return '<span style="font-weight:bold;background-color:' . $cor[$flag] . ';color:black">' . $status[$flag] . '</span>';
        });

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_data_abertura);
        $this->datagrid->addColumn($col_system_user_id);
        $this->datagrid->addColumn($col_ramal);
        $this->datagrid->addColumn($col_problema_id);
        $this->datagrid->addColumn($col_flag);
        $this->datagrid->addColumn($col_data_fechamento);

        $acao_editar = new TDataGridAction(['OcorrenciaFormAdm', 'onEdit'], ['id' => '{id}']);
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
