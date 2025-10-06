<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
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

class TipoInsumoFormList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_TipoInsumo');
        $this->form->setFormTitle(_t('Input type'));

        $id         = new TEntry('id');
        $nome       = new TEntry('nome');

        $id->setSize('80');
        $nome->setSize('250');

        $id->setMask('99 9999');
        $id->setEditable(false);

        $nome->addValidation(_t('Name'), new TRequiredValidator);

        $this->form->addFields( [new TLabel(_t('ID'))],   [$id]);
        $this->form->addFields( [new TLabel(_t('Name').' (*)')], [$nome] );

        $this->form->addAction(_t('Save'),  new TAction([$this, 'onSave']), 'far:save green');
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']),  'fa:eraser blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id         = new TDataGridColumn('id',         _t('ID'),       'center',   '5%');
        $col_nome       = new TDataGridColumn('nome',       _t('Name'),     'left',     '95%');

        $action1 = new TDataGridAction([$this, 'onEdit'],   ['key'=>'{id}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key'=>'{id}'] );
        
        $action1->setUseButton(TRUE);
        $action2->setUseButton(TRUE);
        
        $this->datagrid->addAction($action1, '', 'far:edit blue');
        $this->datagrid->addAction($action2, '', 'far:trash-alt red');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);

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

    public function onDelete($param)
    {
        if((int)$param['id'] > 0)
        {
            $action1 = new TAction(array($this, 'Delete'));
            $action1->setParameter('id', $param['id']);
            
            new TQuestion(_t('Do you want to delete the registration?'), $action1);
        }
        
    }

    public function Delete($param)
    {
        if((int)$param['id'] > 0)
        {
            TTransaction::open('permission');

            $object = new TipoInsumo($param['id']);
            $object->delete();

            TTransaction::close();
            $this->onReload();
        }
    }

    public function onSave($param)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->validate();

            if(!empty($data->nome))
            {
                TTransaction::open('permission');
                $tipo_insumo = TipoInsumo::where('id <> '.(int)$data->id.' and nome','ilike', $data->nome)->first();
                if($tipo_insumo)
                    throw new Exception(_t('There is already a registration with that name, check!'));

                if((int)$data->id > 0)
                    $object = new TipoInsumo($data->id);
                else
                    $object = new TipoInsumo();
                $object->nome = $data->nome;

                $object->save();

                TTransaction::close();

                $data->id   = '';
                $data->nome = '';
                $this->form->setData($data);

                $this->onReload();
            }

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {        
        $this->form->clear(true);
        
        $this->onReload();
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('permission');

            $repository = new TRepository('TipoInsumo');
            $limit = 10;
            $criteria = new TCriteria;

            $criteria->setProperty('limit', $limit);
            $criteria->setProperty('offset', (isset($param['offset'])) ? (int) $param['offset'] : 0);
            $criteria->setProperty('order', 'nome asc');
            
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
        $data = $this->form->getData();
        $key = $param['id'];

        if((int)$key > 0)
        {
            TTransaction::open('permission');

            $object = new TipoInsumo((int)$key);            

            TTransaction::close();
            if($object)
            {
                $data->id = $object->id;
                $data->nome = $object->nome;
            }

            $this->form->setData($data);
        }
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