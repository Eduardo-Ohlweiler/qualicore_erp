<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class PessoaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Pessoa');
        $this->form->setFormTitle('Pesquisar Pessoas');

        $nome = new TEntry('nome');
        $this->form->addFields([new TLabel('Nome:')], [$nome]);

        $this->form->addAction('Pesquisar', new TAction([$this, 'onSearch']), 'fa:search blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_nome = new TDataGridColumn('nome', 'Nome', 'left', '40%');
        $col_data_nascimento = new TDataGridColumn('data_nascimento', 'Data Nascimento', 'center', '20%');
        $col_tipo_pessoa = new TDataGridColumn('tipo_pessoa->nome', 'Tipo Pessoa', 'left', '30%');
        $col_telefone = new TDataGridColumn('telefone', 'Telefone', 'left', '30%');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_data_nascimento);
        $this->datagrid->addColumn($col_tipo_pessoa);

        $action_edit = new TDataGridAction([$this, 'onEdit'], ['id' => '{id}']);
        $this->datagrid->addAction($action_edit, 'Editar', 'fa:edit blue');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        parent::add($vbox);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        $criteria = new TCriteria;

        if (!empty($data->nome)) {
            $criteria->add(new TFilter('nome', 'LIKE', "%{$data->nome}%"));
        }

        TSession::setValue('PessoaList_filter', $criteria);
        $this->onReload();
    }

    public function onReload()
    {
        TTransaction::open('avaliafit');

        $criteria = new TCriteria;
        $repository = new TRepository('Pessoa');
        $limit = 10;
        $param['offset'] = (TSession::getValue('page') - 1) * $limit;
        $param['limit'] = $limit;

        $objects = $repository->load($criteria, $param);
        $this->datagrid->clear();

        if ($objects) {
            foreach ($objects as $object) {
                $this->datagrid->addItem($object);
            }
        }

        $this->pageNavigation->setCount($repository->count($criteria));
        $this->pageNavigation->setProperties(['page' => TSession::getValue('page') ?? 1, 'limit' => $limit]);

        TTransaction::close();
    }

    public function onEdit($param)
    {
        $key = $param['id'];
        //TApplication::loadPage('PessoaForm', 'onEdit', ['key' => $key]);
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
?>