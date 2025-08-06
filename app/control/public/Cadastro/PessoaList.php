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
use Adianti\Widget\Datagrid\TDataGridActionGroup;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
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
        $this->form->setFormTitle(_t('People'));

        $id         = new TEntry('id');
        $nome       = new TEntry('nome');
        $email      = new TEntry('email');
        $telefone   = new TEntry('telefone');
        $cpf_cnpj   = new TEntry('cpf_cnpj');

        $id->setSize('80');
        $nome->setSize('250');
        $email->setSize('250');
        $telefone->setSize('250');
        $cpf_cnpj->setSize('250');

        $id->setMask('99 9999');

        $this->form->addFields( [new TLabel(_t('ID'))],   [$id]);
        $this->form->addFields( [new TLabel(_t('Name'))], [$nome] );
        $this->form->addFields( [new TLabel(_t('Email'))],[$email] );
        $this->form->addFields( [new TLabel(_t('Phone'))],[$telefone] );

        $this->form->addAction(_t('Search'),   new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction(_t('Clear'),    new TAction([$this, 'onClear']),  'fa:eraser blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id         = new TDataGridColumn('id',         _t('ID'),       'center',   '5%');
        $col_nome       = new TDataGridColumn('nome',       _t('Name'),     'left',     '20%');
        $col_email      = new TDataGridColumn('email_list_br',      _t('Email'),    'left',     '20%');
        $col_email->setTransformer(function($value){return $value;});

        $col_telefone   = new TDataGridColumn('telefone_list_br',   _t('Phone'),    'left',     '20%');
        $col_telefone->setTransformer(function($value){return $value;});

        $col_cpf_cnpj   = new TDataGridColumn('cpf_or_cnpj',   _t('Cpf/Cnpj'), 'left',     '20%');
        $col_bloqueado  = new TDataGridColumn('bloqueado_icon',  _t('Blocked'),  'left',     '15%');
        $col_bloqueado->setTransformer(function($value){return $value;});

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_email);
        $this->datagrid->addColumn($col_telefone);
        $this->datagrid->addColumn($col_cpf_cnpj);
        $this->datagrid->addColumn($col_bloqueado);

        $action1 = new TDataGridAction([$this, 'onEdit'],     ['key' => '{id}'] );
        $action2 = new TDataGridAction([$this, 'onBlock'],    ['key' => '{id}' ] );

        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:search blue');
        $action2->setLabel(_t('Block').'/'._t('Unlock'));
        $action2->setImage('fa:ban red');

        $action_group = new TDataGridActionGroup('Ação ', 'fa:th blue');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        
        $this->datagrid->addActionGroup($action_group);

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

    public function onClear($param)
    {
        // Limpa o filtro da sessão
        TSession::setValue('PessoaList_filter', null);
        
        // Limpa os dados do formulário
        $this->form->clear(true);
        
        // Recarrega a listagem
        $this->onReload();
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        $criteria = new TCriteria;

        if ((int)$data->id > 0) 
            $criteria->add(new TFilter('id', '=', $data->id));
        elseif (!empty($data->nome)) 
            $criteria->add(new TFilter('nome', 'ILIKE', "%{$data->nome}%"));
        elseif (!empty($data->email)) 
            $criteria->add(new TFilter('', 'EXISTS', "(SELECT null FROM email e
                                                                                                WHERE e.email ILIKE %{$data->email}% AND e.pessoa_id = pessoa.id)"));
        elseif (!empty($data->telefone)) 
            $criteria->add(new TFilter('', 'EXISTS', "(SELECT null FROM telefone t
                                                                                                WHERE t.numero ILIKE %{$data->telefone}% AND t.pessoa_id = pessoa.id)"));

        TSession::setValue('PessoaList_filter', $criteria);
        $this->onReload();

        $this->form->setData($data);
    }

    public function onBlock($param)
    {
        if((int)$param['id'] > 0)
        {
            $action1 = new TAction(array($this, 'Block'));
            $action1->setParameter('id', $param['id']);
            
            new TQuestion(_t('Do you want to block/unblock the registration?'), $action1);
        }
        
    }

    public function Block($param)
    {
        if((int)$param['id'] > 0)
        {
            TTransaction::open('avaliafit');

            $pessoa = new Pessoa($param['id']);
            if($pessoa)
            {   
                if($pessoa->bloqueado == 2)
                    $pessoa->bloqueado = 1;
                elseif($pessoa->bloqueado == 1)
                    $pessoa->bloqueado = 2;
                
                $pessoa->save();
            }

            TTransaction::close();
            $this->onReload();
        }
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('avaliafit');

            $repository = new TRepository('Pessoa');
            $limit = 10;
            $user = TSession::getValue('userid');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('user_id', '=', $user));

            if (TSession::getValue('PessoaList_filter'))
            {
                $criteria = TSession::getValue('PessoaList_filter');
            }

            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', (isset($param['offset'])) ? (int) $param['offset'] : 0);
            $criteria->setProperty('order', 'id asc');
            
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
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