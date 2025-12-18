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
        $this->form = new BootstrapFormBuilder('form_ConferenciaUsinagemForm');
        $this->form->setFormTitle(_t('Machining Conference'));

        // create the form fields
        $id     = new TEntry('id');
        $retrabalho = new TCombo('bloqueado');
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

//        $vbox->add($this->form);
        $vbox->add($panel = TPanelGroup::pack('', $this->datagrid));
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
