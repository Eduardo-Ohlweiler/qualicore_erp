<?php

use Adianti\Widget\Wrapper\TDBCombo;

class ConferenciaUsinagemForm extends \Adianti\Control\TPage
{
    protected $form;      // form
    protected $datagrid;  // datagrid
    protected $loaded = false;
    protected $pageNavigation;  // pagination component

    public function __construct()
    {
        parent::__construct();

        // create the form
        $this->form = new BootstrapFormBuilder('form_ConferenciaUsinagemDetalhamentoForm');
        $this->form->setFormTitle(_t('Machining Conference'));

        // create the form fields
        $conferencia_usinagem_id    = new TEntry('conferencia_usinagem_id');
        $id                         = new TEntry('id');
        $retrabalho = new TCombo('retrabalho');
        $retrabalho->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $maquina_id             = new TDBCombo('maquina_id', 'permission', 'Maquina', 'id', 'nome');
        $pessoa_id              = new TDBCombo('pessoa_id', 'permission', 'Pessoa', 'id', 'nome');
        $turno_id               = new TDBCombo('turno_id', 'permission', 'Turno', 'id', 'nome');
        $quantidade_retrabalho  = new TEntry('quantidade_retrabalho');
        $quantidade_refugo      = new TEntry('quantidade_refugo');
        $obs                    = new TText('obs');
        $margem_retrabalho      = new TEntry('margem_retrabalho');
        $margem_refugo          = new TEntry('margem_refugo');
        $margem_reprovado       = new TEntry('margem_reprovado');
        $criou_pessoa_id        = new TEntry('criou_pessoa_id');
        $criou_pessoa_nome      = new TEntry('criou_pessoa_nome');
        $alterou_pessoa_id      = new TEntry('alterou_pessoa_id');
        $alterou_pessoa_nome    = new TEntry('alterou_pessoa_nome');
        $criado_em              = new TDate('criado_em');
        $alterado_em            = new TDate('alterado_em');

        // add the form fields
        $this->form->addFields( [new TLabel('ID')],                     [$id, $conferencia_usinagem_id] );
        $this->form->addFields( [new TLabel('Retrabalho')],             [$retrabalho] );
        $this->form->addFields( [new TLabel('Maquina')],                [$maquina_id] );
        $this->form->addFields( [new TLabel('Operador')],               [$pessoa_id] );
        $this->form->addFields( [new TLabel('Turno')],                  [$turno_id] );
        $this->form->addFields( [new TLabel('Quantidade retrabalho')],  [$quantidade_retrabalho] );
        $this->form->addFields( [new TLabel('Quantidade refugo')],      [$quantidade_refugo] );
        $this->form->addFields( [new TLabel('Obs')],                    [$obs] );
        $this->form->addFields( [new TLabel('% Retrabalho')],           [$margem_retrabalho] );
        $this->form->addFields( [new TLabel('% Refugo')],               [$margem_refugo] );
        $this->form->addFields( [new TLabel('% Refugo')],               [$margem_reprovado] );

        $this->form->addFields([new TLabel('Criado')],      [$criou_pessoa_id,      $criou_pessoa_nome,     $criado_em]);
        $this->form->addFields([new TLabel('Alterado')],    [$alterou_pessoa_id,    $alterou_pessoa_nome,   $alterado_em]);

        $id->setSize('80');
        $retrabalho->setSize('100');
        $maquina_id->setSize('80%');
        $pessoa_id->setSize('80%');
        $turno_id->setSize('200');
        $quantidade_retrabalho->setSize('150');
        $quantidade_refugo->setSize('150');
        $margem_retrabalho->setSize('150');
        $margem_refugo->setSize('150');
        $margem_reprovado->setSize('150');
        $obs->setSize('80%');
        $criou_pessoa_id->setSize('80');
        $alterou_pessoa_id->setSize('80');
        $criou_pessoa_nome->setSize('300');
        $alterou_pessoa_nome->setSize('300');

        $maquina_id->addValidation('Maquina',                               new TRequiredValidator());
        $pessoa_id->addValidation('Operador',                               new TRequiredValidator());
        $turno_id->addValidation('Turno',                                   new TRequiredValidator());
        $quantidade_retrabalho->addValidation('Quantidade de retrabalho',   new TRequiredValidator());
        $quantidade_refugo->addValidation('Quantidade de refugo',           new TRequiredValidator());
        $retrabalho->addValidation('Retrabalho',                            new TRequiredValidator());

        // define the form actions
        $this->form->addAction( 'Save', new TAction([$this, 'onSave']), 'fa:save green');

        // make id not editable
        $id->setEditable(FALSE);
        $margem_retrabalho->setEditable(FALSE);
        $margem_refugo->setEditable(FALSE);
        $margem_reprovado->setEditable(FALSE);
        $criou_pessoa_id->setEditable(FALSE);
        $criou_pessoa_nome->setEditable(FALSE);
        $alterou_pessoa_id->setEditable(FALSE);
        $alterou_pessoa_nome->setEditable(FALSE);
        $criado_em->setEditable(FALSE);
        $alterado_em->setEditable(FALSE);
        $conferencia_usinagem_id->setEditable(FALSE);

        $criado_em->setMask('dd/mm/yyyy');
        $criado_em->setDatabaseMask('yyyy-mm-dd');
        $alterado_em->setMask('dd/mm/yyyy');
        $alterado_em->setDatabaseMask('yyyy-mm-dd');

        //========================================================================
        //========================================================================
        //FORM PRINCIPAL
        $this->principal_form = new BootstrapFormBuilder('form_PrincipalConferenciaUsinagemDetalhamentoForm');
        $this->principal_form->setFormTitle(_t('Machining Conference'));

        // create the form fields
        $principal_id           = new TEntry('principal_id');
        $ordem_servico          = new TEntry('ordem_servico');
        $cancelado      = new TCombo('cancelado');
        $cancelado->setChangeAction(new TAction([$this, 'onHabilitaDesabilita']));
        $cancelado->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $motivo_cancelamento_id = new TDBCombo('motivo_cancelamento_id', 'permission', 'MotivoCancelamento', 'id', 'motivo');

        $insumo_id                      = new TDBCombo('insumo_id', 'permission', 'Insumo', 'id', 'descricao', 'descricao asc');
        $quantidade_total               = new TEntry('quantidade_total');
        $quantidade_total->setExitAction(new TAction([$this, 'onCalculaTotais']));

        $quantidade_total_refugo        = new TEntry('quantidade_total_refugo');
        $quantidade_total_retrabalho    = new TEntry('quantidade_total_retrabalho');
        $margem_total_retrabalho        = new TEntry('margem_total_retrabalho');
        $margem_total_refugo            = new TEntry('margem_total_refugo');
        $margem_total_reprovado         = new TEntry('margem_total_reprovado');

        $principal_criou_pessoa_id      = new TEntry('principal_criou_pessoa_id');
        $principal_criou_pessoa_nome    = new TEntry('principal_criou_pessoa_nome');
        $principal_alterou_pessoa_id    = new TEntry('principal_alterou_pessoa_id');
        $principal_alterou_pessoa_nome  = new TEntry('principal_alterou_pessoa_nome');
        $principal_criado_em            = new TDate('principal_criado_em');
        $principal_alterado_em          = new TDate('principal_alterado_em');

        // add the form fields
        $this->principal_form->addFields( [new TLabel('ID')],                             [$principal_id] );
        $this->principal_form->addFields( [new TLabel('Insumo')],                         [$insumo_id] );
        $this->principal_form->addFields( [new TLabel('Ordem de serviço')],               [$ordem_servico] );
        $this->principal_form->addFields( [new TLabel('Quantidade total')],               [$quantidade_total] );
        $this->principal_form->addFields( [new TLabel('Quantidade total de retrabalho')], [$quantidade_total_retrabalho] );
        $this->principal_form->addFields( [new TLabel('Quantidade total de refugo')],     [$quantidade_total_refugo] );
        $this->principal_form->addFields( [new TLabel('% Total retrabalho')],             [$margem_total_retrabalho] );
        $this->principal_form->addFields( [new TLabel('% Total refugo')],                 [$margem_total_refugo] );
        $this->principal_form->addFields( [new TLabel('% Total reprovado')],              [$margem_total_reprovado] );

        $this->principal_form->addFields([new TLabel('Criado')],    [$principal_criou_pessoa_id,      $principal_criou_pessoa_nome,     $principal_criado_em]);
        $this->principal_form->addFields([new TLabel('Alterado')],  [$principal_alterou_pessoa_id,    $principal_alterou_pessoa_nome,   $principal_alterado_em]);

        $this->principal_form->addFields([new TLabel(_t('Canceled'))],                [$cancelado]);
        $this->principal_form->addFields([new TLabel(_t('Reason for cancellation'))], [$motivo_cancelamento_id]);

        $principal_id->setSize('80');
        $cancelado->setSize('100');
        $insumo_id->setSize('80%');
        $ordem_servico->setSize('150');
        $quantidade_total->setSize('150');
        $quantidade_total_retrabalho->setSize('150');
        $quantidade_total_refugo->setSize('150');
        $margem_total_retrabalho->setSize('150');
        $margem_total_refugo->setSize('150');
        $margem_total_reprovado->setSize('150');
        $principal_criou_pessoa_id->setSize('80');
        $principal_criou_pessoa_nome->setSize('300');
        $principal_alterou_pessoa_id->setSize('80');
        $principal_alterou_pessoa_nome->setSize('300');
        $motivo_cancelamento_id->setSize('80%');
        $cancelado->setSize('100');

        $quantidade_total->addValidation(_('Quantidade total'), new TRequiredValidator());
        $insumo_id->addValidation(_('Insumo'),                  new TRequiredValidator());
        $ordem_servico->addValidation(_('Ordem de serviço'),    new TRequiredValidator());


        // define the form actions
        $this->principal_form->addAction( 'Save',       new TAction([$this, 'onSavePrincipal']),    'fa:save green');
        $this->principal_form->addAction( _t('New'),    new TAction([$this, 'onEditCurtain']),      'fa:plus green');


        // make id not editable
        $principal_id->setEditable(FALSE);
        $quantidade_total_retrabalho->setEditable(FALSE);
        $quantidade_total_refugo->setEditable(FALSE);
        $margem_total_retrabalho->setEditable(FALSE);
        $margem_total_refugo->setEditable(FALSE);
        $margem_total_reprovado->setEditable(FALSE);
        $principal_criou_pessoa_id->setEditable(FALSE);
        $principal_criou_pessoa_nome->setEditable(FALSE);
        $principal_alterou_pessoa_id->setEditable(FALSE);
        $principal_alterou_pessoa_nome->setEditable(FALSE);
        $principal_criado_em->setEditable(FALSE);
        $principal_alterado_em->setEditable(FALSE);

        $principal_criado_em->setMask('dd/mm/yyyy');
        $principal_criado_em->setDatabaseMask('yyyy-mm-dd');
        $principal_alterado_em->setMask('dd/mm/yyyy');
        $principal_alterado_em->setDatabaseMask('yyyy-mm-dd');
        @$ordem_servico->setMask('999999999999');

        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        // add the columns
        $col_id    = new TDataGridColumn('id', 'Id', 'right', '10%');
        $col_name  = new TDataGridColumn('name', 'Name', 'left', '90%');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_name);

        // define row actions
        $action1 = new TDataGridAction([$this, 'onEditCurtain'],   ['key' => '{id}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}'] );

        $this->datagrid->addAction($action1, 'Edit',   'far:edit blue');
        $this->datagrid->addAction($action2, 'Delete', 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // wrap objects inside a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
       $vbox->add(new TXMLBreadCrumb('menu.xml', 'ConferenciaUsinagemList'));

        $vbox->add($this->principal_form);
        $vbox->add($panel = TPanelGroup::pack('', $this->datagrid));



        // search box
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');

        // enable fuse search by column name
        $this->datagrid->enableSearch($input_search, 'id, name');
        $panel->addHeaderWidget($input_search);

        // $action = new TAction([$this, 'onEditCurtain']);
        // $panel->addHeaderActionLink(_t('New'), $action, 'fa:plus green');

        // pack the table inside the page
        parent::add($vbox);
    }

    public function onDelete($param)
    {

    }

    public function onSave ($param)
    {
        $data = $this->form->getData();
        $this->form->validate();
        try
        {
            TTransaction::open('permission');

            if((int)$data->id > 0)
            {
                $object                      = new ConferenciaUsinagemDetalhamento($data->id);
                $object->alterado_em         = date('Y-m-d');
                $object->alterado_por        = TSession::getValue('userid');
                $object->alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
                $object->alterou_pessoa_id   = $object->alterado_por;
            }
            else
            {
                $object = new ConferenciaUsinagemDetalhamento();
                $object->criado_em         = date('Y-m-d');
                $object->criado_por        = TSession::getValue('userid');
                $object->criou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
                $object->criou_pessoa_id   = $object->criado_por;
            }

            $object->conferencia_usinagem_id   = (int)$param['principal_id'];
            $object->maquina_id                = (int)$data->maquina_id;
            $object->pessoa_id                 = (int)$data->pessoa_id;
            $object->turno_id                  = (int)$data->turno_id;
            $object->quantidade_retrabalho     = (float)$data->quantidade_retrabalho;
            $object->quantidade_refugo         = (float)$data->quantidade_refugo;
            $object->obs                       = $data->obs;
            $object->retrabalho                = (int)$data->retrabalho;

            $object->store();

            TTransaction::close();

            $this->form->setData($data);
            $this->onReload($param);
            new TMessage('info', _('Record saved'));

        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            $this->form->setData($data);
            new TMessage('error', $e->getMessage());
        }
    }

    public function onReload($param = null)
    {
        TTransaction::open('permission');

        $criteria = new TCriteria;
        $criteria->add(new TFilter('conferencia_usinagem_id', '=', $param['principal_id']));

        $repo = new TRepository('ConferenciaUsinagemDetalhamento');
        $objects = $repo->load($criteria);

        $this->datagrid->clear();
        foreach ($objects as $obj)
        {
            $this->datagrid->addItem($obj);
        }

        TTransaction::close();
        $this->loaded = true;
    }

    public static function onCalculaTotais($param)
    {
        try
        {
            $obj = new stdClass();

            $quantidade_total = 0;
            if((int)$param['quantidade_total'] > 0)
                $quantidade_total = $param['quantidade_total'];
            
            $quantidade_total_retrabalho    = 0;
            $quantidade_total_refugo        = 0;

            $margem_total_retrabalho        = 0;
            $margem_total_refugo            = 0;
            $margem_total_reprovado         = 0;

            $obj->quantidade_total_retrabalho = 0;
            $obj->margem_total_retrabalho = 0;
            $obj->quantidade_total_refugo = 0;
            $obj->margem_total_refugo = 0;

            if((int)$param['principal_id'] > 0)
            {
                TTransaction::open('permission');

                $conferencia_usinagem_detalhes = ConferenciaUsinagemDetalhamento::where('conferencia_usinagem_id', '=', (int)$param['principal_id'])->load();
                foreach ($conferencia_usinagem_detalhes as $detalle)
                {
                    $quantidade_total_retrabalho += (float)$detalle->quantidade_retrabalho;
                    $quantidade_total_refugo     += (float)$detalle->quantidade_refugo;
                }

                TTransaction::close();
                if ((int)$quantidade_total_retrabalho > 0)
                {
                    $obj->quantidade_total_retrabalho = $quantidade_total_retrabalho;
                    $obj->margem_total_retrabalho = FuncoesCalculos::calcularMargensRetFloat($param['quantidade_total'], $quantidade_total_retrabalho, 2);
                }

                if ((int)$quantidade_total_refugo > 0)
                {
                    $obj->quantidade_total_refugo = $quantidade_total_refugo;
                    $obj->margem_total_refugo = FuncoesCalculos::calcularMargensRetFloat($param['quantidade_total'], $quantidade_total_refugo, 2);
                }   
            }

            $obj->margem_total_reprovado = (float)$quantidade_total_retrabalho + (float)$quantidade_total_refugo;
            TForm::sendData('form_PrincipalConferenciaUsinagemDetalhamentoForm', $obj);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSavePrincipal($param)
    {
        $this->principal_form->validate();
        $data = $this->principal_form->getData();
        try
        {
            if ((int)$data->cancelado == 1 && (int)$data->motivo_cancelamento_id == 0)
                throw new Exception(_t("To cancel, please provide a reason for cancellation."));

            TTransaction::open('permission');

            $conferencia_usinagem_salva = ConferenciaUsinagem::where("ordem_servico = ".$data->ordem_servico." and id ",'<>', (int)$data->principal_id)->first();
            if($conferencia_usinagem_salva)
                throw new Exception(_('Já existe um registro para essa ordem de serviço, verifique!'));

            if((int)$data->principal_id > 0)
            {
                $object                      = new ConferenciaUsinagem($data->principal_id);
                $object->alterado_em         = date('Y-m-d');
                $object->alterado_por        = TSession::getValue('userid');
                $object->principal_alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
                $object->principal_alterou_pessoa_id = $object->alterado_por;
                $object->principal_alterado_em = $object->alterado_em;
            }
            else
            {
                $object = new ConferenciaUsinagem();
                $object->criado_em         = date('Y-m-d');
                $object->criado_por        = TSession::getValue('userid');
                $object->principal_criou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
                $object->principal_criou_pessoa_id   = $object->criado_por;
                $object->principal_criado_em           = $object->criado_em;
            }

            $object->ordem_servico          = (int)$data->ordem_servico;
            $object->quantidade_total       = (int)$data->quantidade_total;
            $object->quantidade_refugo      = (int)$data->quantidade_total_refugo;
            $object->quantidade_retrabalho  = (int)$data->quantidade_total_retrabalho;
            $object->cancelado              = (int)$data->cancelado;
            $object->insumo_id              = (int)$data->insumo_id;

            if((int)$data->cancelado == 1)
                $object->motivo_cancelamento_id = (int)$data->motivo_cancelamento_id;

            $object->store();

            TTransaction::close();

            $object->principal_id                = $object->id;
            
            $this->onReload($param);
            $this->principal_form->setData($object);
            self::onHabilitaDesabilita($param);
            new TMessage('info', _('Record saved'));

        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            $this->principal_form->setData($data);
            self::onHabilitaDesabilita($param);
            new TMessage('error', $e->getMessage());
        }

    }

    /**
     *
     */

    public static function onEditCurtain($param = null)
    {
        if((int)$param['principal_id'] == 0)
        {
            new TMessage('error', _('Please save the main form before adding details.'));
            return;
        }

        try
        {
            // create empty page for right panel
            $page = TPage::create();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('override', 'true');
            $page->setPageName(__CLASS__);

            $btn_close = new TButton('closeCurtain');
            $btn_close->onClick = "Template.closeRightPanel();";
            $btn_close->setLabel("Fechar");
            $btn_close->setImage('fas:times');

            // instantiate self class, run method to populate
            $embed = new self($param);
            $embed->form->addHeaderWidget($btn_close);
            $embed->EditCurtain($param);

            // embed form and show page at right panel
            $page->add($embed->form);
            $page->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function EditCurtain($param)
    {
        echo 'teste';
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) 
            {
                $data = $this->form->getData();
                $key = $param['key'];

                TTransaction::open('permission');
                $object = new ConferenciaUsinagem($key);

                $data->principal_id                 = $key;
                $data->ordem_servico                = $object->ordem_servico;
                $data->insumo_id                    = $object->insumo_id;
                $data->quantidade_total             = $object->quantidade_total;
                $data->quantidade_total_retrabalho  = $object->quantidade_retrabalho;
                $data->quantidade_total_refugo      = $object->quantidade_total_refugo;
                $data->cancelado                    = $object->cancelado;
                $data->motivo_cancelamento_id       = $object->motivo_cancelamento_id;

                if((int)$object->criado_por > 0)
                {
                    $data->principal_criou_pessoa_id   = $object->criado_por;
                    $data->principal_criou_pessoa_nome = SystemUser::find($object->criado_por)->name;
                    $data->principal_criado_em         = $object->criado_em;
                }
                if((int)$object->alterado_por > 0)
                {
                    $data->principal_alterou_pessoa_id   = $object->alterado_por;
                    $data->principal_alterou_pessoa_nome = SystemUser::find($object->alterado_por)->name;
                    $data->principal_alterado_em         = $object->alterado_em;
                }
                $this->principal_form->setData($data);
                TTransaction::close();

                $param['principal_id']      = $key;
                $param['quantidade_total']  = $data->quantidade_total;
                
                self::onCalculaTotais($param);                
            } else {
                $this->form->clear(TRUE);
            }
            self::onHabilitaDesabilita($param);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onHabilitaDesabilita($param = null)
    {
        try {
            $formName = 'form_PrincipalConferenciaUsinagemDetalhamentoForm';

            $obj = new stdClass();

            if (isset($param['cancelado']) && $param['cancelado'] == 1)
            {
                TDBCombo::enableField($formName, 'motivo_cancelamento_id');
            }
            else
            {
                TDBCombo::disableField($formName, 'motivo_cancelamento_id');
                $obj->motivo_cancelamento_id = '';
            }

            TForm::sendData($formName, $obj);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
        if (!$this->loaded)
        {
            $this->onReload($param);
        }
    }
}
