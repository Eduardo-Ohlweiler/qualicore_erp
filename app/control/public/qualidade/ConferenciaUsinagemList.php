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

        $this->form = new BootstrapFormBuilder('form_search_ConferenciaUsinagem');
        $this->form->setFormTitle(_t('Machining Conference'));

        $id             = new TEntry('id');
        $insumo_id      = new TDBUniqueSearch('insumo_id', 'permission', 'Insumo', 'id', 'descricao');
        $codigo         = new TEntry('codigo');
        $data1          = new TDate('data1');
        $data2          = new TDate('data2');
        $ordem_servico  = new TEntry('ordem_servico');

        $id->setSize('80');
        $insumo_id->setSize('80%');
        $codigo->setSize('200');
        $data1->setSize('150');
        $data2->setSize('150');
        $ordem_servico->setSize('200');

        $id->setMask('99 9999');
        $data1->setMask('dd/mm/yyyy');
        $data2->setMask('dd/mm/yyyy');

        $this->form->addFields( [new TLabel(_t('ID'))],          [$id]);
        $this->form->addFields( [new TLabel(_t('Date'))],[$data1, $data2]);
        $this->form->addFields( [new TLabel(_t('Description'))], [$insumo_id]);
        $this->form->addFields( [new TLabel(_t('Drawing code'))],[$codigo]);
        $this->form->addFields( [new TLabel(_t('Service order'))],[$ordem_servico]);


        $this->form->addAction(_t('Search'),   new TAction([$this, 'onSearch']), 'fa:search blue');
        $this->form->addAction(_t('Clear'),    new TAction([$this, 'onClear']),  'fa:eraser blue');
        $this->form->addAction(_t('New'),      new TAction([$this, 'onNew']),    'fa:plus green');
        $this->form->addAction(_('CSV'),      new TAction([$this, 'onCsv']),    'fa:table blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id                     = new TDataGridColumn('id',                    _t('ID'),   'center', '5%');
        $col_data_conferencia       = new TDataGridColumn('criado_em',      _t('Date'), 'left',  '6%');
        $col_ordem_servico          = new TDataGridColumn('ordem_servico',         _('Ordem de serviço'), 'right',  '10%');
        $col_insumo_id              = new TDataGridColumn('insumo_descricao_id_cod_desenho',   _('Insumo'), 'left',  '30%');
        $col_quantidade_total       = new TDataGridColumn('quantidade_total',      _('Quantidade total'), 'right',  '8%');
        $col_quantidade_refugo      = new TDataGridColumn('quantidade_refugo',     _('Quantidade de refugo'), 'right',  '8%');
        $col_quantidade_retrabalho  = new TDataGridColumn('quantidade_retrabalho', _('Quantidade de retrabalho'), 'right',  '7%');
        $col_margem_retrabalho      = new TDataGridColumn('margem_retrabalho',     _('% Retrabalho'),        'right',     '7%');
        $col_margem_refugo          = new TDataGridColumn('margem_refugo',         _('% Refugo'),        'right',     '7%');
        $col_margem_rejeicao        = new TDataGridColumn('margem_rejeicao',       _('% Rejeição'),        'right',     '7%');

        $col_bloqueado   = new TDataGridColumn('bloqueado_icon',  _t('Canceled'),  'left',     '5%');
        $col_bloqueado->setTransformer(function($value){return $value;});

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_data_conferencia);
        $this->datagrid->addColumn($col_ordem_servico);
        $this->datagrid->addColumn($col_insumo_id);
        $this->datagrid->addColumn($col_quantidade_total);
        $this->datagrid->addColumn($col_quantidade_refugo);
        $this->datagrid->addColumn($col_quantidade_retrabalho);
        $this->datagrid->addColumn($col_margem_retrabalho);
        $this->datagrid->addColumn($col_margem_refugo);
        $this->datagrid->addColumn($col_margem_rejeicao);
        $this->datagrid->addColumn($col_bloqueado);

        $action1 = new TDataGridAction([$this, 'onEdit'],     ['key' => '{id}'] );
        $action2 = new TDataGridAction([$this, 'onBlock'],    ['key' => '{id}' ] );

        $action1->setLabel(_t('Edit'));
        $action1->setImage('fa:search blue');
        $action2->setLabel(_t('Block').'/'._t('Unlock'));
        $action2->setImage('fa:ban red');

        $action_group = new TDataGridActionGroup('Ação ', 'fa:th blue');
        $action_group->addAction($action1);

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

    public function onCsv($param)
    {
        try
        {
            $data = $this->datagrid->getOutputData();

            if (!$data)
            {
                new TMessage('info', 'Nenhum dado para exportar');
                return;
            }

            $dir  = '/var/www/html/qualicore_erp/app/output';
            $timestamp = date('Ymd_His');
            $file = $dir . "/conferencia_usinagem_{$timestamp}.csv";

            if (!is_dir($dir) || !is_writable($dir)) {
                throw new Exception('Diretório de exportação não existe ou não tem permissão');
            }

            $handler = fopen($file, 'w');
            if (!$handler) {
                throw new Exception('Não foi possível criar o arquivo CSV');
            }

            // cabeçalho
            fputcsv($handler, array_keys((array) $data[0]), ';');

            foreach ($data as $row)
            {
                $row = (array) $row;

                if (!empty($row['data_conferencia'])) {
                    $row['data_conferencia'] = TDate::date2br($row['data_conferencia']);
                }

                fputcsv($handler, $row, ';');
            }

            fclose($handler);

            parent::openFile($file);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }



    public function onClear($param)
    {
        $data = $this->form->getData();
        TSession::setValue('ConferenciaUsinagemList_filter', null);
        TSession::setValue('ConferenciaUsinagemList_data',   null);

        $data->id               = '';
        $data->insumo_id        = '';
        $data->codigo           = '';
        $data->data1            = '';
        $data->data2            = '';
        $data->ordem_servico    = '';

        $this->form->clear(true);
        $this->onReload();
        $this->form->setData($data);
    }

    public function onSearch()
    {
        $data = $this->form->getData();
        $criteria = new TCriteria;

        if ((int)$data->id > 0) 
            $criteria->add(new TFilter('id', '=', $data->id));
        if (!empty($data->descricao)) 
            $criteria->add(new TFilter('descricao', 'ILIKE', "%{$data->descricao}%"));
        if ((int)$data->insumo_id > 0)
            $criteria->add(new TFilter('insumo_id', '=', $data->insumo_id));
        if (!empty($data->codigo))
        {
            $criteria->add(new TFilter(
                'insumo_id',
                'IN',
                "(SELECT id FROM insumo WHERE codigo ILIKE '%{$data->codigo}%')"
            ));
        }

        if( !empty($data->data1) || !empty($data->data2))
        {
            if(!empty($data->data1))
            {
                $criteria->add(new TFilter('', 'exists', "(SELECT null FROM conferencia_usinagem_detalhamento cud
                                                                                    WHERE criado_em >= ".$data->data1." and cud.conferencia_usinagem_id = conferencia_usinagem.id)"));
            }
            if(!empty($data->data2))
            {
                $criteria->add(new TFilter('', 'exists', "(SELECT null FROM conferencia_usinagem_detalhamento cud
                                                                                    WHERE criado_em <= ".$data->data2." and cud.conferencia_usinagem_id = conferencia_usinagem.id)"));
            }
        }

        if (!empty($data->ordem_servico))
        {
            $criteria->add(
                new TFilter(
                    'CAST(ordem_servico AS TEXT)',
                    'ILIKE',
                    "%{$data->ordem_servico}%"
                )
            );
        }

        TSession::setValue('ConferenciaUsinagemList_filter', $criteria);
        TSession::setValue('ConferenciaUsinagemList_data', $data);
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

            $conferencia_usinagem = new ConferenciaUsinagem($param['id']);
            if($conferencia_usinagem)
            {   
                if($conferencia_usinagem->cancelado != 1)
                    $conferencia_usinagem->cancelado = 1;
                elseif($conferencia_usinagem->cancelado == 1)
                    $conferencia_usinagem->cancelado = 2;

                $conferencia_usinagem->save();
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

            if (TSession::getValue('ConferenciaUsinagemList_filter'))
                $criteria = TSession::getValue('ConferenciaUsinagemList_filter');
            if (TSession::getValue('ConferenciaUsinagemList_data'))
                $data = TSession::getValue('ConferenciaUsinagemList_data');

            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', (isset($param['offset'])) ? (int) $param['offset'] : 0);
            $criteria->setProperty('order', 'id desc');
            
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $margem_refugo      = FuncoesCalculos::calcularMargensRetFloat($object->quantidade_total, $object->quantidade_refugo, 2);
                    $margem_retrabalho  = FuncoesCalculos::calcularMargensRetFloat($object->quantidade_total, $object->quantidade_refugo, 2);
                    $margem_rejeicao    = $margem_refugo + $margem_retrabalho;
                    $object->margem_refugo = $margem_retrabalho.' %';
                    $object->margem_retrabalho = $margem_refugo.' %';
                    $object->margem_rejeicao = $margem_rejeicao.' %';
                    $object->criado_em = TDate::date2br($object->criado_em);
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
        TApplication::loadPage('ConferenciaUsinagemForm', 'onEdit', ['key' => $key]);
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