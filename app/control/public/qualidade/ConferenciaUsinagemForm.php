<?php
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

        $this->setDatabase('permission'); // define the database
        $this->setActiveRecord('ConferenciaUsinagemDetalhamento'); // define the Active Record
        $this->setDefaultOrder('id', 'asc'); // define the default order
        $this->setLimit(-1); // turn off limit for datagrid

        // create the form
        $this->form = new BootstrapFormBuilder('form_ConferenciaUsinagemForm');
        $this->form->setFormTitle(_t('Standard Form/DataGrid'));

        // create the form fields
        $id     = new TEntry('id');
        $name   = new TEntry('name');

        // add the form fields
        $this->form->addFields( [new TLabel('ID')],    [$id] );
        $this->form->addFields( [new TLabel('Name', 'red')],  [$name] );

        $name->addValidation('Name', new \Adianti\Validator\TRequiredValidator());

        // define the form actions
        $this->form->addAction( 'Save', new TAction([$this, 'onSave']), 'fa:save green');

        // make id not editable
        $id->setEditable(FALSE);

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

        //$vbox->add($this->form);

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
