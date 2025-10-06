<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Util\TXMLBreadCrumb;

class PessoaForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_Pessoa');
        $this->form->setFormTitle(_t('Person registration'));

        $id             = new TEntry('id');
        $nome           = new TEntry('nome');
        $bloqueado      = new TCombo('bloqueado');
        $bloqueado->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $criou_pessoa_id     = new TEntry('criou_pessoa_id');
        $criou_pessoa_nome   = new TEntry('criou_pessoa_nome');
        $alterou_pessoa_id   = new TEntry('alterou_pessoa_id');
        $alterou_pessoa_nome = new TEntry('alterou_pessoa_nome');
        $criado_em           = new TDate('criado_em');
        $alterado_em         = new TDate('alterado_em');

        $id->setEditable(FALSE);
        $criou_pessoa_id->setEditable(FALSE);
        $criou_pessoa_nome->setEditable(FALSE);
        $alterou_pessoa_id->setEditable(FALSE);
        $alterou_pessoa_nome->setEditable(FALSE);
        $criado_em->setEditable(FALSE);
        $alterado_em->setEditable(FALSE);

        $criado_em->setMask('dd/mm/yyyy');
        $criado_em->setDatabaseMask('yyyy-mm-dd');
        $alterado_em->setMask('dd/mm/yyyy');
        $alterado_em->setDatabaseMask('yyyy-mm-dd');

        $nome->addValidation(_t('Name'), new TRequiredValidator);

        $id->setSize('80');
        $bloqueado->setSize('100');
        $nome->setSize('80%');
        $criou_pessoa_id->setSize('80');
        $alterou_pessoa_id->setSize('80');
        $criou_pessoa_nome->setSize('300');
        $alterou_pessoa_nome->setSize('300');

        $this->form->addFields([new TLabel(_t('ID'))],          [$id]);
        $this->form->addFields([new TLabel(_t('Name').' (*)')], [$nome]);
        $this->form->addFields([new TLabel(_t('Blocked'))],     [$bloqueado]);

        $this->form->addFields([new TLabel('Criado')],   [$criou_pessoa_id,   $criou_pessoa_nome,   $criado_em]);
        $this->form->addFields([new TLabel('Alterado')], [$alterou_pessoa_id, $alterou_pessoa_nome, $alterado_em]);

        $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction(_t('New'), new TAction([$this, 'onClear']), 'fa:plus blue');
        $this->form->addAction(_t('To go back'), new TAction(['PessoaList', 'onReload']), 'fa:arrow-left blue');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'InsumoList'));
        $vbox->add($this->form);

        parent::add($vbox);
    }

    /**
     * Salvar registro
     */
    public function onSave($param)
    {
        try {
            $data = $this->form->getData();
            $this->form->validate();

            TTransaction::open('permission');

            if((int)$data->id > 0)
            {
                $object = new Pessoa($data->id);
                $object->alterado_em       = date('Y-m-d');
                $object->alterou_pessoa_id = TSession::getValue('userid');

                $object->alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
            }
            else 
            {
                $object = new Pessoa();
                $object->criado_em       = date('Y-m-d');
                $object->criou_pessoa_id = TSession::getValue('userid');
                $object->criou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
            }
            $object->nome      = $data->nome;
            $object->bloqueado = $data->bloqueado ?? 2;
            $object->store();
            TTransaction::close();            

            $this->form->setData($object);
            new TMessage('info', 'Registro salvo com sucesso!');

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Carregar para edição
     */
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) 
            {
                $data = $this->form->getData();
                $key = $param['key'];

                TTransaction::open('permission');
                $object = new Pessoa($key);

                $data->id             = $key;
                $data->nome         = $object->nome;
                $data->bloqueado      = $object->bloqueado;

                if((int)$object->criou_pessoa_id > 0)
                {
                    $data->criou_pessoa_id   = $object->criou_pessoa_id;
                    $data->criou_pessoa_nome = SystemUser::find($object->criou_pessoa_id)->name;
                    $data->criado_em         = $object->criado_em;
                }
                if((int)$object->alterou_pessoa_id > 0)
                {
                    $data->alterou_pessoa_id = $object->alterou_pessoa_id;
                    $data->alterou_pessoa_nome = SystemUser::find($object->alterou_pessoa_id)->name;
                    $data->alterado_em         = $object->alterado_em;
                }
                $this->form->setData($data);
                TTransaction::close();
                
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }
}
