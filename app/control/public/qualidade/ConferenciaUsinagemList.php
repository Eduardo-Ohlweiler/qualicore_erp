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
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class ConferenciaUsinagemList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Insumo');
        $this->form->setFormTitle(_t('Inputs'));

        $id             = new TEntry('id');
        //$decricao       = new TEntry('descricao');
        //$codigo         = new TEntry('codigo');
        //$tipo_insumo_id = new TDBCombo('tipo_insumo_id', 'permission', 'TipoInsumo', 'id', 'nome');

        $id->setSize('80');
        //$decricao->setSize('250');
        //$codigo->setSize('250');
        //$tipo_insumo_id->setSize('250');

        $id->setMask('99 9999');

        $this->form->addFields( [new TLabel(_t('ID'))],          [$id]);
        //$this->form->addFields( [new TLabel(_t('Drawing code'))],[$codigo] );
        //$this->form->addFields( [new TLabel(_t('Description'))], [$decricao] );
        //$this->form->addFields( [new TLabel(_t('Input type'))],  [$tipo_insumo_id] );

        $this->form->addAction(_t('Search'),   new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction(_t('Clear'),    new TAction([$this, 'onClear']),  'fa:eraser blue');
        $this->form->addAction(_t('New'),      new TAction([$this, 'onNew']),    'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id          = new TDataGridColumn('id',               _t('ID'),          'center',   '5%');
        //$col_codigo      = new TDataGridColumn('codigo',           _t('Drawing code'),        'left',     '20%');
        //$col_descricao   = new TDataGridColumn('descricao',        _t('Description'), 'left',     '20%');
        //$col_tipo_insumo = new TDataGridColumn('tipo_insumo->nome', _t('Input type'),  'left',     '20%');

        $col_bloqueado   = new TDataGridColumn('bloqueado_icon',  _t('Blocked'),  'left',     '15%');
        $col_bloqueado->setTransformer(function($value){return $value;});

        $this->datagrid->addColumn($col_id);
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
        TSession::setValue('InsumoList_filter', null);
        TSession::setValue('InsumoList_data',   null);
        
        $this->form->clear(true);
        $this->onReload();
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        $criteria = new TCriteria;

        if ((int)$data->id > 0) 
            $criteria->add(new TFilter('id', '=', $data->id));
        if (!empty($data->descricao)) 
            $criteria->add(new TFilter('descricao', 'ILIKE', "%{$data->descricao}%"));
        if ((int)$data->tipo_insumo_id > 0) 
            $criteria->add(new TFilter('tipo_insumo_id', '=', $data->tipo_insumo_id));
        if (!empty($data->codigo)) 
            $criteria->add(new TFilter('codigo', 'ILIKE', "%{$data->codigo}%"));

        TSession::setValue('InsumoList_filter', $criteria);
        TSession::setValue('InsumoList_data', $data);
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

    public function onNew()
    {
        TApplication::loadPage('ConferenciaUsinagemForm', 'onEdit', ['key' => null]);        
    }

    public function Block($param)
    {
        if((int)$param['id'] > 0)
        {
            TTransaction::open('permission');

            $insumo = new Insumo($param['id']);
            if($insumo)
            {   
                if($insumo->bloqueado == 2)
                    $insumo->bloqueado = 1;
                elseif($insumo->bloqueado == 1)
                    $insumo->bloqueado = 2;
                
                $insumo->save();
            }

            TTransaction::close();
            $this->onReload();
        }
    }

    public function onReload($param = null)
    {
        try
        {
            $data = $this->form->getData();
            TTransaction::open('permission');

            $repository = new TRepository('ConferenciaUsinagem');
            $limit = 10;
            $criteria = new TCriteria;

            if (TSession::getValue('InsumoList_filter'))
                $criteria = TSession::getValue('InsumoList_filter');
            if (TSession::getValue('InsumoList_data'))
                $data = TSession::getValue('InsumoList_data');

            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', (isset($param['offset'])) ? (int) $param['offset'] : 0);
            $criteria->setProperty('order', 'id desc');
            
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
            $this->form->setData($data);
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
        TApplication::loadPage('InsumoForm', 'onEdit', ['key' => $key]);
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