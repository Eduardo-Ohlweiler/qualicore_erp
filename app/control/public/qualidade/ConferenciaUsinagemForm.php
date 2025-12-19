<?php

use Adianti\Widget\Wrapper\TDBCombo;

class ConferenciaUsinagemForm extends \Adianti\Control\TPage
{
    protected $form;      // form
    protected $datagrid;  // datagrid
    protected $loaded;
    protected $pageNavigation;  // pagination component

    use Adianti\Base\AdiantiStandardFormListTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();

//        $this->setDatabase('permission'); // define the database
//        $this->setActiveRecord('ConferenciaUsinagemDetalhamento'); // define the Active Record
//        $this->setDefaultOrder('id', 'asc'); // define the default order
//        $this->setLimit(-1); // turn off limit for datagrid

        // create the form
        $this->form = new BootstrapFormBuilder('form_ConferenciaUsinagemDetalhamentoForm');
        $this->form->setFormTitle(_t('Machining Conference'));

        // create the form fields
        $id     = new TEntry('id');
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
        $margem_retrabalho     = new TEntry('margem_retrabalho');
        $margem_refugo     = new TEntry('margem_refugo');
        $margem_reprovado     = new TEntry('margem_reprovado');

        $criou_pessoa_id     = new TEntry('criou_pessoa_id');
        $criou_pessoa_nome   = new TEntry('criou_pessoa_nome');
        $alterou_pessoa_id   = new TEntry('alterou_pessoa_id');
        $alterou_pessoa_nome = new TEntry('alterou_pessoa_nome');
        $criado_em           = new TDate('criado_em');
        $alterado_em         = new TDate('alterado_em');

        // add the form fields
        $this->form->addFields( [new TLabel('ID')],    [$id] );
        $this->form->addFields( [new TLabel('Retrabalho')],    [$retrabalho] );
        $this->form->addFields( [new TLabel('Maquina')],    [$maquina_id] );
        $this->form->addFields( [new TLabel('Operador')],    [$pessoa_id] );
        $this->form->addFields( [new TLabel('Turno')],    [$turno_id] );
        $this->form->addFields( [new TLabel('Quantidade retrabalho')],    [$quantidade_retrabalho] );
        $this->form->addFields( [new TLabel('Quantidade refugo')],    [$quantidade_refugo] );
        $this->form->addFields( [new TLabel('Obs')],    [$obs] );
        $this->form->addFields( [new TLabel('% Retrabalho')],    [$margem_retrabalho] );
        $this->form->addFields( [new TLabel('% Refugo')],    [$margem_refugo] );
        $this->form->addFields( [new TLabel('% Refugo')],    [$margem_reprovado] );

        $this->form->addFields([new TLabel('Criado')], [$criou_pessoa_id, $criou_pessoa_nome, $criado_em]);
        $this->form->addFields([new TLabel('Alterado')], [$alterou_pessoa_id, $alterou_pessoa_nome, $alterado_em]);

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

        $maquina_id->addValidation('Name', new TRequiredValidator());
        $pessoa_id->addValidation('Name', new TRequiredValidator());
        $turno_id->addValidation('Name', new TRequiredValidator());
        $quantidade_retrabalho->addValidation('Name', new TRequiredValidator());
        $quantidade_refugo->addValidation('Name', new TRequiredValidator());
        $retrabalho->addValidation('Name', new TRequiredValidator());

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

        $criado_em->setMask('dd/mm/yyyy');
        $criado_em->setDatabaseMask('yyyy-mm-dd');
        $alterado_em->setMask('dd/mm/yyyy');
        $alterado_em->setDatabaseMask('yyyy-mm-dd');

        //========================================================================
        //========================================================================
        //FORM PRINCIPAL
        $this->principal_form = new BootstrapFormBuilder('form_ConferenciaUsinagemDetalhamentoForm');
        $this->principal_form->setFormTitle(_t('Machining Conference'));

        // create the form fields
        $principal_id           = new TEntry('principal_id');
        $ordem_servico          = new TEntry('ordem_servico');
        $cancelado              = new TCombo('cancelado');
        $cancelado->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $insumo_id                      = new TDBCombo('insumo_id', 'permission', 'Insumo', 'id', 'nome');
        $quantidade_total               = new TEntry('quantidade_total');
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

        $quantidade_total->addValidation(_('Quantidade total'), new TRequiredValidator());
        $insumo_id->addValidation(_('Insumo'), new TRequiredValidator());


        // define the form actions
        $this->principal_form->addAction( 'Save', new TAction([$this, 'onSavePrincipal']), 'fa:save green');

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

        // create the datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        // add the columns
        $col_id    = new TDataGridColumn('id', 'Id', 'right', '10%');
        $col_name  = new TDataGridColumn('name', 'Name', 'left', '90%');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_name);

        $col_id->setAction( new TAction([$this, 'onReload']),   ['order' => 'id']);
        $col_name->setAction( new TAction([$this, 'onReload']), ['order' => 'name']);

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
//        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));

        $vbox->add($this->principal_form);
//        $vbox->add($panel = TPanelGroup::pack('', $this->datagrid));
        $vbox->add($panel = TPanelGroup::pack('', $this->datagrid));



        // search box
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');

        // enable fuse search by column name
        $this->datagrid->enableSearch($input_search, 'id, name');
        $panel->addHeaderWidget($input_search);

        $panel->addHeaderActionLink(_t('New'), new TAction([$this, 'onEditCurtain']), 'fa:plus green');

        // pack the table inside the page
        parent::add($vbox);
    }

    public function onSavePrincipal()
    {
        $this->principal_form->validate();
        $data = $this->principal_form->getData();

        $conferencia_usinagem_salva = ConferenciaUsinagem::where("ordem_servico = ".$data->ordem_servico." and id ",'<>', $data->principal_id)->first();
        if($conferencia_usinagem_salva)
            throw new Exception(_('Já existe um registro para essa ordem de serviço, verifique!'));

        if((int)$data->principal_id > 0)
        {
            $object = new ConferenciaUsinagem($data->principal_id);
            $object->alterado_em       = date('Y-m-d');
            $object->alterou_pessoa_id = TSession::getValue('userid');

            $object->alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
        }
        else
        {
            $object = new ConferenciaUsinagem();
            $object->criado_em       = date('Y-m-d');
            $object->criou_pessoa_id = TSession::getValue('userid');
            $object->criou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
        }

        $object->ordem_servico = $data->ordem_servico;
//
//cancelado
//insumo_id
//quantidade_total
//quantidade_total_refugo
//quantidade_total_retrabalho
//margem_total_retrabalho
//margem_total_refugo
//margem_total_reprovado
//principal_criou_pessoa_id
//principal_criou_pessoa_nome
//principal_alterou_pessoa_id
//principal_alterou_pessoa_nome
//principal_criado_em
//principal_alterado_em
    }

    /**
     *
     */
    public static function onEditCurtain($param = null)
    {
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
            $embed->onEdit($param);

            // embed form and show page at right panel
            $page->add($embed->form);
            $page->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
