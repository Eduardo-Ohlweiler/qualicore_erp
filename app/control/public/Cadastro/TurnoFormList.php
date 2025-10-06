<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

class TurnoFormList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_search_Turno');
        $this->form->setFormTitle(_t('Shift'));

        $id         = new TEntry('id');
        $nome       = new TEntry('nome');

        $bloqueado      = new TCombo('bloqueado');
        $bloqueado->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $id->setSize('80');
        $nome->setSize('250');
        $bloqueado->setSize('100');

        $id->setMask('99 9999');
        $id->setEditable(false);

        $nome->addValidation(_t('Name'), new TRequiredValidator);
        $bloqueado->addValidation(_t('Blocked'), new TRequiredValidator);

        $this->form->addFields( [new TLabel(_t('ID'))],             [$id]);
        $this->form->addFields( [new TLabel(_t('Name')  .' (*)')],  [$nome] );
        $this->form->addFields([new TLabel(_t('Blocked').' (*)')],  [$bloqueado]);

        $this->form->addAction(_t('Save'),  new TAction([$this, 'onSave']), 'far:save green');
        $this->form->addAction(_t('Clear'), new TAction([$this, 'onClear']),  'fa:eraser blue');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        $col_id         = new TDataGridColumn('id',         _t('ID'),       'center',   '5%');
        $col_nome       = new TDataGridColumn('nome',       _t('Name'),     'left',     '95%');
        $col_bloqueado  = new TDataGridColumn('bloqueado_icon',  _t('Blocked'),  'left',     '15%');
        $col_bloqueado->setTransformer(function($value){return $value;});

        $action1 = new TDataGridAction([$this, 'onEdit'],   ['key'=>'{id}'] );
        $action2 = new TDataGridAction([$this, 'onDelete'], ['key'=>'{id}'] );
        
        $action1->setUseButton(TRUE);
        $action2->setUseButton(TRUE);
        
        $this->datagrid->addAction($action1, '', 'far:edit blue');
        $this->datagrid->addAction($action2, '', 'far:trash-alt red');

        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_bloqueado);

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

            $object = new Turno($param['id']);
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
                $turno = Turno::where('id <> '.(int)$data->id.' and nome','ilike', $data->nome)->first();
                if($turno)
                    throw new Exception(_t('There is already a registration with that name, check!'));

                if((int)$data->id > 0)
                    $object = new Turno($data->id);
                else
                    $object = new Turno();
                $object->nome       = $data->nome;
                $object->bloqueado  = $data->bloqueado;

                $object->save();

                TTransaction::close();

                $data->id        = '';
                $data->nome      = '';
                $data->bloqueado = '';
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

            $repository = new TRepository('Turno');
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

            $object = new Turno((int)$key);            

            TTransaction::close();
            if($object)
            {
                $data->id        = $object->id;
                $data->nome      = $object->nome;
                $data->bloqueado = $object->bloqueado;
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