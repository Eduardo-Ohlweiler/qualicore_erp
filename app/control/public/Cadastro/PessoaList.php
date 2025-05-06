<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class PessoaList extends TPage
{
    private $form; // formulário de filtro
    private $datagrid; // listagem
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        // Criação do formulário
        $this->form = new BootstrapFormBuilder('form_search_cliente');
        $this->form->setFormTitle('Clientes');

        $nome = new TEntry('nome');
        $email = new TEntry('email');

        $this->form->addFields([new TLabel('Nome:')], [$nome]);
        $this->form->addFields([new TLabel('Email:')], [$email]);

        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');

        // Criação do Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setHeight(320);

        $this->datagrid->addColumn(new TDataGridColumn('id', 'ID', 'right', '50px'));
        $this->datagrid->addColumn(new TDataGridColumn('nome', 'Nome', 'left'));
        $this->datagrid->addColumn(new TDataGridColumn('email', 'Email', 'left'));

        $action = new TDataGridAction([$this, 'onEdit'], ['id' => '{id}']);
        $this->datagrid->addAction($action, 'Editar', 'fa:edit blue');

        // Criação da navegação
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // Container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);

        parent::add($container);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        TSession::setValue('ClienteList_filter_nome', $data->nome);
        TSession::setValue('ClienteList_filter_email', $data->email);
        $this->form->setData($data);
        $this->onReload();
    }

    public function onReload($param = null)
    {
        try {
            TTransaction::open('avaliafit');

            $repository = new TRepository('Pessoa');
            $limit = 10;
            $criteria = new TCriteria;
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);

            $nome = TSession::getValue('ClienteList_filter_nome');
            $email = TSession::getValue('ClienteList_filter_email');

            if ($nome) {
                $criteria->add(new TFilter('nome', 'like', "%{$nome}%"));
            }
            if ($email) {
                $criteria->add(new TFilter('email', 'like', "%{$email}%"));
            }

            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
    {
        new TMessage('info', 'Você clicou para editar o ID: ' . $param['id']);
    }

    public function show()
    {
        $this->onReload();
        parent::show();
    }

}
