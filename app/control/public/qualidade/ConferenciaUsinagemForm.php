<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFieldList;
use Adianti\Widget\Form\TForm;
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

        $id                  = new TEntry('id');
        $data_conferencia    = new TDate('data_conferencia');
        $insumo_id           = new TDBCombo('insumo_id', 'permission', 'Insumo', 'id', 'codigo_descricao');
        $quantidade_total    = new TEntry('quantidade_total');
        $ordem_servico       = new TEntry('ordem_servico');

        $criado_por          = new TEntry('criado_por');
        $criou_pessoa_nome   = new TEntry('criou_pessoa_nome');
        $alterado_por        = new TEntry('alterado_por');
        $alterou_pessoa_nome = new TEntry('alterou_pessoa_nome');
        $criado_em           = new TDate('criado_em');
        $alterado_em         = new TDate('alterado_em');

        $cancelado      = new TCombo('cancelado');
        $cancelado->setChangeAction(new TAction([$this, 'onHabilitaDesabilita']));
        $cancelado->addItems([
            1 => _t('Yes'),
            2 => _t('No')
        ]);

        $motivo_cancelamento_id = new TDBCombo('motivo_cancelamento_id', 'permission', 'MotivoCancelamento', 'id', 'motivo');
        //$motivo_cancelamento_id->enableSearch();

        $id->setEditable(FALSE);
        $criado_por->setEditable(FALSE);
        $criou_pessoa_nome->setEditable(FALSE);
        $alterado_por->setEditable(FALSE);
        $alterou_pessoa_nome->setEditable(FALSE);
        $criado_em->setEditable(FALSE);
        $alterado_em->setEditable(FALSE);

        $criado_em->setMask('dd/mm/yyyy');
        $criado_em->setDatabaseMask('yyyy-mm-dd');
        $alterado_em->setMask('dd/mm/yyyy');
        $alterado_em->setDatabaseMask('yyyy-mm-dd');
        $quantidade_total->setMask('9999999999');
        $ordem_servico->setMask('999999999999');
        $data_conferencia->setMask('dd/mm/yyyy');
        $data_conferencia->setDatabaseMask('yyyy-mm-dd');

        $data_conferencia->addValidation(_t('Date'),           new TRequiredValidator);
        $quantidade_total->addValidation(_t('Total quantity'), new TRequiredValidator);

        $id->setSize('80');
        $criado_por->setSize('80');
        $alterado_por->setSize('80');
        $criou_pessoa_nome->setSize('300');
        $alterou_pessoa_nome->setSize('300');
        $data_conferencia->setSize('150');
        $insumo_id->setSize('80%');
        $motivo_cancelamento_id->setSize('80%');
        $quantidade_total->setSize('100');
        $cancelado->setSize('100');
        $ordem_servico->setSize('150');

        $this->form->addFields([new TLabel(_t('ID'))],          [$id]);
        $this->form->addFields([new TLabel(_t('Date').' (*)')],        [$data_conferencia]);
        $this->form->addFields([new TLabel(_t('Service order'))],        [$ordem_servico]);
        $this->form->addFields([new TLabel(_t('Part'))],        [$insumo_id]);
        $this->form->addFields([new TLabel(_t('Total quantity').' (*)')], [$quantidade_total]);
        $this->form->addFields([new TLabel(_t('Canceled'))], [$cancelado]);
        $this->form->addFields([new TLabel(_t('Reason for cancellation'))], [$motivo_cancelamento_id]);

        $this->form->addFields([new TLabel('Criado')],   [$criado_por,   $criou_pessoa_nome,   $criado_em]);
        $this->form->addFields([new TLabel('Alterado')], [$alterado_por, $alterou_pessoa_nome, $alterado_em]);

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

        $detalhamento_retrabalho_sim_nao = new TCombo('detalhamento_retrabalho_sim_nao[]');
        $detalhamento_retrabalho_sim_nao->enableSearch();
        $detalhamento_retrabalho_sim_nao->addItems(['1'=>'<b>'._t('Yes').'</b>','2'=>'<b>'._t('No').'</b>']);
        $detalhamento_retrabalho_sim_nao->setSize('100%');
        
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
        
        $this->fieldlist = new TFieldList();
        $this->fieldlist->generateAria();
        $this->fieldlist->width = '100%';
        $this->fieldlist->name  = 'my_field_list';
        $this->fieldlist->addField( '<b>'._t('Operator').' (*)</b>',            $detalhamento_pessoa_id,        ['width' => '180'] );        
        $this->fieldlist->addField( '<b>'._t('Machine').' (*)</b>',             $detalhamento_maquina_id,       ['width' => '150'] );
        $this->fieldlist->addField( '<b>'._t('Shift').' (*)</b>',               $detalhamento_turno_id,         ['width' => '100'] );        
        $this->fieldlist->addField( '<b>'._t('Rework quantity').' (*)</b>',     $quantidade_retrabalho,         ['width' => '50', 'sum' => true] );
        $this->fieldlist->addField( '<b>'._t('Amount of scrap').' (*)</b>',     $quantidade_refugo,             ['width' => '50', 'sum' => true] );

        $this->fieldlist->addField( '<b>% Reprovado</b>',  $margem_reprovacao, ['width' => '80', 'sum' => true] );
        $this->fieldlist->addField( '<b>% Retrabalho</b>', $margem_retrabalho, ['width' => '80', 'sum' => true] );

        $this->fieldlist->addField( '<b>'._t('Rework').'</b>',              $detalhamento_retrabalho_sim_nao,           ['width' => '80'] );
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

    public static function onHabilitaDesabilita($param = null)
{
    try {
        $formName = 'form_ConferenciaUsinagemForm';

        $obj = new stdClass();

        if (isset($param['cancelado']) && $param['cancelado'] == 1) 
        {
            TDBCombo::enableField($formName, 'motivo_cancelamento_id');
        } 
        else 
        {
            TDBCombo::disableField($formName, 'motivo_cancelamento_id');
            $obj->motivo_cancelamento_id = '';
        }

        TForm::sendData($formName, $obj);
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage());
    }
}

    /**
     * Salvar registro
     */
    public function onSave($param)
    {
        try {
            $data = $this->form->getData();
            if((int)$data->cancelado == 1 && (int)$data->motivo_cancelamento_id == 0)
                throw new Exception(_t("To cancel, please provide a reason for cancellation."));

            $this->form->validate();
            TTransaction::open('permission');
            $conferencia_usinagem_salvo = ConferenciaUsinagem::where('id <> '.(int)$data->id.' and ordem_servico','=', $data->ordem_servico)->first();
            if($conferencia_usinagem_salvo)
                throw new Exception(_t("There is already a conference with this service order, check it!"));
            
            if((int)$data->id > 0)
            {
                $object                      = new ConferenciaUsinagem($data->id);
                $object->alterado_em         = date('Y-m-d');
                $object->alterado_por        = TSession::getValue('userid');
                $object->alterou_pessoa_nome = SystemUser::find(TSession::getValue('userid'))->name;
            }
            else 
            {
                $object                     = new ConferenciaUsinagem();
                $object->criado_em          = date('Y-m-d');
                $object->criado_por         = TSession::getValue('userid');
                $object->criou_pessoa_nome  = SystemUser::find(TSession::getValue('userid'))->name;
            }

            $object->data_conferencia       = $data->data_conferencia;
            $object->ordem_servico          = $data->ordem_servico;
            $object->insumo_id              = $data->insumo_id;
            $object->quantidade_total       = $data->quantidade_total;
            $object->cancelado              = 2;
            if((int)$data->cancelado > 0)
                $object->cancelado          = $data->cancelado;
            $object->motivo_cancelamento_id = $data->motivo_cancelamento_id;
            $object->store();

            //----------------- Detalhamento ------------------
            ConferenciaUsinagemDetalhamento::where('conferencia_usinagem_id', '=', $object->id)->delete();

            if ( !empty($param['detalhamento_pessoa_id']) ) 
            {
                foreach ($param['detalhamento_pessoa_id'] as $key => $pessoa_id) 
                {
                    if (empty($param['detalhamento_pessoa_id'][$key]) || (int)$param['detalhamento_pessoa_id'][$key] == 0)
                        throw new Exception(_t("Informe o operador."));
                    if (empty($param['detalhamento_maquina_id'][$key]) || (int)$param['detalhamento_maquina_id'][$key] == 0)
                        throw new Exception(_t("Informe a máquina."));
                    if (empty($param['detalhamento_turno_id'][$key]) || (int)$param['detalhamento_turno_id'][$key] == 0)
                        throw new Exception(_t("Informe o turno."));
                    if (empty($param['quantidade_retrabalho'][$key]) || (int)$param['quantidade_retrabalho'][$key] == 0)
                        throw new Exception(_t("Informe a quantidade de retrabalho."));
                    if (empty($param['quantidade_refugo'][$key]) || (int)$param['quantidade_refugo'][$key] == 0)
                        throw new Exception(_t("Informe a quantidade de refugo."));

                    $detalhe = new ConferenciaUsinagemDetalhamento;
                    $detalhe->conferencia_usinagem_id = $object->id;
                    $detalhe->pessoa_id               = $param['detalhamento_pessoa_id'][$key];
                    $detalhe->maquina_id              = $param['detalhamento_maquina_id'][$key];
                    $detalhe->turno_id                = $param['detalhamento_turno_id'][$key];
                    $detalhe->quantidade_retrabalho   = (int)$param['quantidade_retrabalho'][$key];
                    $detalhe->quantidade_refugo       = (int)$param['quantidade_refugo'][$key];

                    // Calcula margens
                    $detalhe->margem_retrabalho = 0;
                    $detalhe->margem_reprovacao = 0;

                    if((int)$param['quantidade_retrabalho'][$key] > 0)
                        $detalhe->margem_retrabalho = FuncoesCalculos::calcularMargensRetFloat((int)$param['quantidade_retrabalho'][$key], $object->quantidade_total, 2);
                    if((int)$param['quantidade_refugo'][$key] > 0)
                        $detalhe->margem_reprovacao = FuncoesCalculos::calcularMargensRetFloat((int)$param['quantidade_refugo'][$key],     $object->quantidade_total, 2);;
                        

                    $detalhe->refugo_sim_nao = $param['detalhamento_refugo_sim_nao'][$key] ?? 2;
                    $detalhe->store();
                }
            }
            
        
            TTransaction::close();            

            $this->form->setData($object);
            new TMessage('info', _t('Record saved successfully!'));
            self::onHabilitaDesabilita($param);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
            $this->form->setData($data);
            self::onHabilitaDesabilita($param);            
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
            self::onHabilitaDesabilita();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
        self::onHabilitaDesabilita();
    }
}
