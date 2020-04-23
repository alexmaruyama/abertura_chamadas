<?php

use Adianti\Base\AdiantiFileSaveTrait;
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TMultiFile;

class OcorrenciaForm extends TPage
{
    private $form;

    use AdiantiFileSaveTrait;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_ocorrencia');
        $this->form->setFormTitle('Ocorrências');

        $id = new TEntry('id');
        $data_abertura = new TDateTime('data_abertura');
        $data_fechamento = new TDateTime('data_fechamento');
        $ramal = new TEntry('ramal');
        $descricao = new TText('descricao');
        $problema_id = new TDBUniqueSearch('problema_id', 'banco', 'Problema', 'id', 'nome', 'nome');
        $anexos = new TMultiFile('anexos');

        $id->setEditable(false);
        $data_abertura->setMask('dd/mm/yyyy');
        $data_abertura->setDatabaseMask('yyyy-mm-dd');
        $data_fechamento->setMask('dd/mm/yyyy');
        $data_fechamento->setDatabaseMask('yyyy-mm-dd');
        $problema_id->setMinLength(0);
        $data_abertura->setEditable(false);
        $data_fechamento->setEditable(false);
        $anexos->setAllowedExtensions(['jpg', 'jpeg', 'png']);
        $anexos->enableFileHandling();
        $anexos->enableImageGallery();

        $data_abertura->setValue(date('d/m/Y H:i'));

        $id->setSize('25%');
        $descricao->setSize(null, '100');

        $ramal->addValidation('Ramal', new TRequiredValidator);
        $problema_id->addValidation('Problema', new TRequiredValidator);

        $this->form->appendPage('Dados');
        $this->form->addFields([new TLabel('ID')], [$id]);
        $this->form->addFields([new TLabel('Data abertura')], [$data_abertura], [new TLabel('Data fechamento')], [$data_fechamento]);
        $this->form->addFields([new TLabel('Ramal', 'red')], [$ramal], [new TLabel('Problema', 'red')], [$problema_id]);
        $this->form->addFields([new TLabel('Descrição do problema')], [$descricao]);

        $this->form->appendPage('Anexos imagens');
        $this->form->addFields([new TLabel('Anexos')], [$anexos]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }

    public function onEdit($param)
    {
        if (isset($param['id'])) {
            try {
                TTransaction::open('banco');
                $ocorrencia = new Ocorrencia($param['id']);
                $ocorrencia->anexos = Anexo::where('ocorrencia_id', '=', $ocorrencia->id)->getIndexedArray('caminho_anexo');
                $this->form->setData($ocorrencia);
                TTransaction::close();
            } catch (Exception $ex) {
                new TMessage('error', $ex->getMessage());
            }
        } else {
            $this->onClear();
        }
    }

    public function onSave($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();

            TTransaction::open('banco');

            $ocorrencia = new Ocorrencia();
            $ocorrencia->fromArray((array) $data);
            $ocorrencia->system_user_id = TSession::getValue('userid');
            $ocorrencia->flag = 0;
            $ocorrencia->store();
            $this->saveFiles($ocorrencia, $data, 'anexos', 'files/anexos', 'Anexo', 'caminho_anexo', 'ocorrencia_id');
            $data->id = $ocorrencia->id;
            $this->form->setData($data);

            TTransaction::close();
            $acao = new TAction(['OcorrenciaList', 'onReload']);
            new TMessage('info', 'Registro salvo com sucesso', $acao);
        } catch (Exception $ex) {
            new TMessage('error', $ex->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear()
    {
        $this->form->clear(true);
    }
}
