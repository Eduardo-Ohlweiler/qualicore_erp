<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFieldList;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Util\TXMLBreadCrumb;

class ConferenciaUsinagemForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_ConferenciaUsinagemForm');
        $this->form->setFormTitle(_t('Machining conference'));

        $id             = new TEntry('id');
        $data = new TDate('data');
        $insumo_id = new TDBCombo('insumo_id', 'permission', 'Insumo', 'id', 'codigo_descricao');
        $quantidade_total = new TEntry('quantidade_total');

        $criou_pessoa_id     = new TEntry('criou_pessoa_id');
        $criou_pessoa_nome   = new TEntry('criou_pessoa_nome');
        $alterou_pessoa_id   = new TEntry('alterou_pessoa_id');
        $alterou_pessoa_nome = new TEntry('alterou_pessoa_nome');
        $criado_em           = new TDate('criado_em');
        $alterado_em         = new TDate('alterado_em');

        $id->setEditable(FALSE);
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
        $quantidade_total->setMask('9999999999');
        $data->setMask('dd/mm/yyyy');
        $data->setDatabaseMask('yyyy-mm-dd');

        //$nome->addValidation(_t('Name'), new TRequiredValidator);

        $id->setSize('80');
        $criou_pessoa_id->setSize('80');
        $alterou_pessoa_id->setSize('80');
        $criou_pessoa_nome->setSize('300');
        $alterou_pessoa_nome->setSize('300');
        $data->setSize('150');
        $insumo_id->setSize('80%');
        $quantidade_total->setSize('100');

        $this->form->addFields([new TLabel(_t('ID'))],          [$id]);
        $this->form->addFields([new TLabel(_t('Date'))],        [$data]);
        $this->form->addFields([new TLabel(_t('Part'))],        [$insumo_id]);
        $this->form->addFields([new TLabel(_t('Total quantity'))], [$quantidade_total]);

        $this->form->addFields([new TLabel('Criado')],   [$criou_pessoa_id,   $criou_pessoa_nome,   $criado_em]);
        $this->form->addFields([new TLabel('Alterado')], [$alterou_pessoa_id, $alterou_pessoa_nome, $alterado_em]);

        // ====================================================
        // =================== DETALHAMENTO ===================
        // ====================================================

        $detalhamento_id                        = new THidden('detalhamento_id[]');
        $detalhamento_conferencia_usinagem_id   = new THidden('detalhamento_conferencia_usinagem_id[]');

        $detalhamento_maquina_id = new TDBCombo('detalhamento_maquina_id[]', 'permission', 'Maquina', 'id', 'nome');
        $detalhamento_maquina_id->enableSearch();
        $detalhamento_maquina_id->setSize('100%');

        $detalhamento_pessoa_id = new TDBCombo('detalhamento_pessoa_id[]', 'permission', 'Pessoa', 'id', 'nome_id');
        $detalhamento_pessoa_id->enableSearch();
        $detalhamento_pessoa_id->setSize('100%');

        $detalhamento_turno_id = new TDBCombo('detalhamento_turno_id[]', 'permission', 'Turno', 'id', 'nome');
        $detalhamento_turno_id->enableSearch();
        $detalhamento_turno_id->setSize('100%');

        $detalhamento_refugo_sim_nao = new TCombo('detalhamento_refugo_sim_nao[]');
        $detalhamento_refugo_sim_nao->enableSearch();
        $detalhamento_refugo_sim_nao->addItems(['1'=>'<b>'._t('Yes').'</b>','2'=>'<b>'._t('No').'</b>']);
        $detalhamento_refugo_sim_nao->setSize('100%');
        
        $quantidade_retrabalho = new TEntry('quantidade_retrabalho[]');
        $quantidade_retrabalho->setMask('9999999999');
        $quantidade_retrabalho->setSize('100%');
        $quantidade_retrabalho->style = 'text-align: right';

        $quantidade_refugo = new TEntry('quantidade_refugo[]');
        $quantidade_refugo->setMask('9999999999');
        $quantidade_refugo->setSize('100%');
        $quantidade_refugo->style = 'text-align: right';

        $margem_reprovacao = new TEntry('margem_reprovacao[]');
        $margem_reprovacao->setNumericMask(2,',','.', true);
        $margem_reprovacao->setSize('100%');
        $margem_reprovacao->style = 'text-align: right';
        $margem_reprovacao->setEditable(false);

        $margem_retrabalho = new TEntry('margem_retrabalho[]');
        $margem_retrabalho->setNumericMask(2,',','.', true);
        $margem_retrabalho->setSize('100%');
        $margem_retrabalho->style = 'text-align: right';
        $margem_retrabalho->setEditable(false);
        
        $detalhamento_data = new TDate('detalhamento_data[]');
        $detalhamento_data->setSize('100%');
        $detalhamento_data->setMask('dd/mm/yyyy');
        $detalhamento_data->setDatabaseMask('yyyy-mm-dd');
        
        $this->fieldlist = new TFieldList();
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField( '<b>'._t('Date').' (*)</b>',            $detalhamento_data,             ['width' => '150'] );
        $this->fieldlist->addField( '<b>'._t('Operator').'</b>',            $detalhamento_pessoa_id,        ['width' => '180'] );        
        $this->fieldlist->addField( '<b>'._t('Machine').'</b>',             $detalhamento_maquina_id,       ['width' => '150'] );
        $this->fieldlist->addField( '<b>'._t('Shift').'</b>',               $detalhamento_turno_id,         ['width' => '100'] );        
        $this->fieldlist->addField( '<b>'._t('Rework quantity').' (*)</b>', $quantidade_retrabalho,         ['width' => '50', 'sum' => true] );
        $this->fieldlist->addField( '<b>'._t('Amount of scrap').' (*)</b>', $quantidade_refugo,             ['width' => '50', 'sum' => true] );

        $this->fieldlist->addField( '<b>% Reprovado</b>', $margem_reprovacao, ['width' => '80', 'sum' => true] );
        $this->fieldlist->addField( '<b>% Retrabalho</b>', $margem_retrabalho, ['width' => '80', 'sum' => true] );

        $this->fieldlist->addField( '<b>'._t('Rework').'</b>',              $detalhamento_refugo_sim_nao,           ['width' => '80'] );
        $this->fieldlist->addField( '<b>Detalhamento id</b>',                    $detalhamento_id,               ['width' => '0%', 'uniqid' => true] );
        $this->fieldlist->addField( '<b>Detalhamento id</b>',                    $detalhamento_conferencia_usinagem_id,['width' => '0%'] );
        
        // $this->fieldlist->setTotalUpdateAction(new TAction([$this, 'x']));
        
        $this->fieldlist->enableSorting();
        //$this->form->addField($data);
        $this->form->addField($detalhamento_maquina_id);
        
                
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();
        
        // add field list to the form
        $this->form->addContent( [$this->fieldlist] );

        // ====================================================
        // ================= FIM DETALHAMENTO =================
        // ====================================================

        $this->form->addAction(_t('Save'),       new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction(_t('New'),        new TAction([$this, 'onClear']), 'fa:plus blue');
        $this->form->addAction(_t('To go back'), new TAction(['ConferenciaUsinagemList', 'onReload']), 'fa:arrow-left blue');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'ConferenciaUsinagemList'));
        $vbox->add($this->form);

        parent::add($vbox);
    }

    public static function showRow($param)
    {
        new TMessage('info', str_replace('","', '",<br>&nbsp;"', json_encode($param)));
    }

    /**
     * Salvar registro
     */
    public function onSave($param)
    {
        try {
            $data = $this->form->getData();
            $this->form->validate();

            TTransaction::open('permission');

            if((int)$data->id > 0)
            {
                $object                      = new ConferenciaUsinagem($data->id);
                $object->alterado_em         = date('Y-m-d');
                $object->alterou_pessoa_id   = TSession::getValue('userid');
                $object->alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
            }
            else 
            {
                $object                     = new ConferenciaUsinagem();
                $object->criado_em          = date('Y-m-d');
                $object->criou_pessoa_id    = TSession::getValue('userid');
                $object->criou_pessoa_nome  = SystemUser::find(TSession::getValue('userid'))->name;
            }
            $object->nome      = $data->nome;
            $object->store();
            TTransaction::close();            

            $this->form->setData($object);
            new TMessage('info', _t('Record saved successfully!'));

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Carregar para edição
     */
    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) 
            {
                $data = $this->form->getData();
                $key = $param['key'];

                TTransaction::open('permission');
                $object     = new ConferenciaUsinagem($key);

                $data->id   = $key;

                if((int)$object->criou_pessoa_id > 0)
                {
                    $data->criou_pessoa_id   = $object->criou_pessoa_id;
                    $data->criou_pessoa_nome = SystemUser::find($object->criou_pessoa_id)->name;
                    $data->criado_em         = $object->criado_em;
                }
                if((int)$object->alterou_pessoa_id > 0)
                {
                    $data->alterou_pessoa_id    = $object->alterou_pessoa_id;
                    $data->alterou_pessoa_nome  = SystemUser::find($object->alterou_pessoa_id)->name;
                    $data->alterado_em          = $object->alterado_em;
                }
                $this->form->setData($data);
                TTransaction::close();
                
            } else {
                $this->form->clear(TRUE);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }
}
