<?php
use Adianti\Core\AdiantiCoreTranslator;

/**
 * ApplicationTranslator
 *
 * @version    8.1
 * @package    util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class ApplicationTranslator
{
    private static $instance; // singleton instance
    private $lang;            // target language
    private $messages;
    private $sourceMessages;
    
    /**
     * Class Constructor
     */
    private function __construct()
    {        

        $this->messages = [];
        $this->messages['en'] = [];
        $this->messages['pt'] = [];
        $this->messages['es'] = [];

        $this->messages['en'][] = 'There is already a conference with this service order, check it!';
        $this->messages['pt'][] = 'Já existe uma conferência com esta ordem de serviço, verifique!';
        $this->messages['es'][] = 'Ya existe una conferencia con esta orden de servicio, ¡consúltela!';

        $this->messages['en'][] = 'To cancel, please provide a reason for cancellation.';
        $this->messages['pt'][] = 'Para cancelar informe um motivo de cancelamento.';
        $this->messages['es'][] = 'Para cancelar, proporcione un motivo de cancelación.';

        $this->messages['en'][] = 'Service order';
        $this->messages['pt'][] = 'Ordem de serviço';
        $this->messages['es'][] = 'Orden de servicio';

        $this->messages['en'][] = 'Reason for cancellation';
        $this->messages['pt'][] = 'Motivo do cancelamento';
        $this->messages['es'][] = 'Motivo de la cancelación';

        $this->messages['en'][] = 'Canceled';
        $this->messages['pt'][] = 'Cancelado';
        $this->messages['es'][] = 'Cancelado';

        $this->messages['en'][] = 'Total quantity';
        $this->messages['pt'][] = 'Quantidade total';
        $this->messages['es'][] = 'Cantidad total';

        $this->messages['en'][] = 'Part';
        $this->messages['pt'][] = 'Peça';
        $this->messages['es'][] = 'Parte';

        $this->messages['en'][] = 'Observation';
        $this->messages['pt'][] = 'Observação';
        $this->messages['es'][] = 'Observación';

        $this->messages['en'][] = 'Rework';
        $this->messages['pt'][] = 'Retrabalho';
        $this->messages['es'][] = 'Rehacer';

        $this->messages['en'][] = 'Operator';
        $this->messages['pt'][] = 'Operador';
        $this->messages['es'][] = 'Operador';

        $this->messages['en'][] = 'Amount of scrap';
        $this->messages['pt'][] = 'Quantidade de refugo';
        $this->messages['es'][] = 'Cantidad de chatarra';

        $this->messages['en'][] = 'Rework quantity';
        $this->messages['pt'][] = 'Quantidade de retrabalho';
        $this->messages['es'][] = 'Cantidad de retrabajo';

        $this->messages['en'][] = 'Record saved successfully!';
        $this->messages['pt'][] = 'Registro salvo com sucesso!';
        $this->messages['es'][] = '¡Registro guardado exitosamente!';

        $this->messages['en'][] = 'Machining conference';
        $this->messages['pt'][] = 'Conferência de usinagem';
        $this->messages['es'][] = 'conferencia de mecanizado';

        $this->messages['en'][] = 'Shift';
        $this->messages['pt'][] = 'Turno';
        $this->messages['es'][] = 'Cambio';

        $this->messages['en'][] = 'Reason';
        $this->messages['pt'][] = 'Motivo';
        $this->messages['es'][] = 'Razón';

        $this->messages['en'][] = 'Reason for Cancellation';
        $this->messages['pt'][] = 'Motivo Cancelamento';
        $this->messages['es'][] = 'Motivo de la cancelación';

        $this->messages['en'][] = 'To go back';
        $this->messages['pt'][] = 'Voltar';
        $this->messages['es'][] = 'Volver';

        $this->messages['en'][] = 'Save';
        $this->messages['pt'][] = 'Salvar';
        $this->messages['es'][] = 'Ahorrar';

        $this->messages['en'][] = 'Person registration';
        $this->messages['pt'][] = 'Cadastro de pessoa';
        $this->messages['es'][] = 'Registro de persona';

        $this->messages['en'][] = 'Machine';
        $this->messages['pt'][] = 'Máquina';
        $this->messages['es'][] = 'Máquina';

        $this->messages['en'][] = 'Quality assistant';
        $this->messages['pt'][] = 'Auxiliar qualidade';
        $this->messages['es'][] = 'Asistente de calidad';

        $this->messages['en'][] = 'Types of non-compliance';
        $this->messages['pt'][] = 'Tipos de não conformidade';
        $this->messages['es'][] = 'Tipos de incumplimiento';

        $this->messages['en'][] = 'Machining notes';
        $this->messages['pt'][] = 'Apontamentos usinagem';
        $this->messages['es'][] = 'Notas de mecanizado';

        $this->messages['en'][] = 'Conferences';
        $this->messages['pt'][] = 'Conferências';
        $this->messages['es'][] = 'Conferencias';

        $this->messages['en'][] = 'Machining Conference';
        $this->messages['pt'][] = 'Conferência usinagem';
        $this->messages['es'][] = 'Conferencia de mecanizado';

        $this->messages['en'][] = 'Quality';
        $this->messages['pt'][] = 'Qualidade';
        $this->messages['es'][] = 'Calidad';

        $this->messages['pt'][] = 'Cadastro de Insumo';
        $this->messages['en'][] = 'Input Registration';
        $this->messages['es'][] = 'Registro de entrada';

        $this->messages['pt'][] = 'Cadastro de Insumo';
        $this->messages['en'][] = 'Input Registration';
        $this->messages['es'][] = 'Registro de entrada';

        $this->messages['pt'][] = 'Código desenho';
        $this->messages['en'][] = 'Drawing code';
        $this->messages['es'][] = 'Codigo de dibujo';

        $this->messages['en'][] = 'ID';
        $this->messages['pt'][] = 'Cód';
        $this->messages['es'][] = 'Id';

        $this->messages['en'][] = 'Name';
        $this->messages['pt'][] = 'Nome';
        $this->messages['es'][] = 'Nombre';

        $this->messages['en'][] = 'Products';
        $this->messages['pt'][] = 'Produtos';
        $this->messages['es'][] = 'Productos';

        $this->messages['en'][] = 'Product';
        $this->messages['pt'][] = 'Produto';
        $this->messages['es'][] = 'Producto';

        $this->messages['en'][] = 'Inputs';
        $this->messages['pt'][] = 'Insumos';
        $this->messages['es'][] = 'Entradas';

        $this->messages['en'][] = 'Description';
        $this->messages['pt'][] = 'Descrição';
        $this->messages['es'][] = 'Descripción';

        $this->messages['en'][] = 'Code';
        $this->messages['pt'][] = 'Código';
        $this->messages['es'][] = 'Código';

        $this->messages['en'][] = 'Email';
        $this->messages['pt'][] = 'Email';
        $this->messages['es'][] = 'Correo electrónico';

        $this->messages['en'][] = 'Phone';
        $this->messages['pt'][] = 'Telefone';
        $this->messages['es'][] = 'Teléfono';

        $this->messages['en'][] = 'People';
        $this->messages['pt'][] = 'Pessoas';
        $this->messages['es'][] = 'Gente';

        $this->messages['en'][] = 'Person';
        $this->messages['pt'][] = 'Pessoa';
        $this->messages['es'][] = 'Persona';

        $this->messages['en'][] = 'Registration assistant';
        $this->messages['pt'][] = 'Auxiliar cadastros';
        $this->messages['es'][] = 'Asistente de registro';

        $this->messages['en'][] = 'Type of person';
        $this->messages['pt'][] = 'Tipo pessoa';
        $this->messages['es'][] = 'Tipo de persona';

        $this->messages['en'][] = 'Registrations';
        $this->messages['pt'][] = 'Cadastros';
        $this->messages['es'][] = 'Inscripciones';

        $this->messages['en'][] = 'Registration type';
        $this->messages['pt'][] = 'Tipo cadastro';
        $this->messages['es'][] = 'Tipo de registro';
        
        $this->messages['en'][] = 'University';
        $this->messages['pt'][] = 'Universidade';
        $this->messages['es'][] = 'Universidad';
        
        $this->messages['en'][] = 'City';
        $this->messages['pt'][] = 'Cidade';
        $this->messages['es'][] = 'Ciudad';

        $this->messages['en'][] = 'Cpf/Cnpj';
        $this->messages['pt'][] = 'Cpf/Cnpj';
        $this->messages['es'][] = 'Cpf/Cnpj';

        $this->messages['en'][] = 'Blocked';
        $this->messages['pt'][] = 'Bloqueado';
        $this->messages['es'][] = 'Obstruido';

        $this->messages['en'][] = 'Block';
        $this->messages['pt'][] = 'Bloquear';
        $this->messages['es'][] = 'Bloquear';

        $this->messages['en'][] = 'Edit';
        $this->messages['pt'][] = 'Editar';
        $this->messages['es'][] = 'Editar';

        $this->messages['en'][] = 'Search';
        $this->messages['pt'][] = 'Buscar';
        $this->messages['es'][] = 'Buscar';

        $this->messages['en'][] = 'Clear';
        $this->messages['pt'][] = 'Limpar';
        $this->messages['es'][] = 'Limpiar';

        $this->messages['en'][] = 'Unlock';
        $this->messages['pt'][] = 'Desbloquear';
        $this->messages['es'][] = 'Desbloquear';

        $this->messages['en'][] = 'Email type';
        $this->messages['pt'][] = 'Tipo email';
        $this->messages['es'][] = 'Tipo de correo electrónico';

        $this->messages['en'][] = 'Phone type';
        $this->messages['pt'][] = 'Tipo telefone';
        $this->messages['es'][] = 'Tipo de teléfono';

        $this->messages['en'][] = 'Address type';
        $this->messages['pt'][] = 'Tipo endereço';
        $this->messages['es'][] = 'Tipo de dirección';

        $this->messages['en'][] = 'Input type';
        $this->messages['pt'][] = 'Tipo insumo';
        $this->messages['es'][] = 'Tipo de entrada';

        $this->messages['en'][] = 'Help products';
        $this->messages['pt'][] = 'Auxiliar produtos';
        $this->messages['es'][] = 'Productos de ayuda';

        $this->messages['en'][] = 'Do you want to block/unblock the registration?';
        $this->messages['pt'][] = 'Deseja bloquear/desbloquear o cadastro?';
        $this->messages['es'][] = '¿Quieres bloquear/desbloquear el registro?';

        $this->messages['en'][] = 'Do you want to delete the registration?';
        $this->messages['pt'][] = 'Deseja excluir o cadastro?';
        $this->messages['es'][] = '¿Quieres eliminar el registro??';

        $this->messages['en'][] = 'There is already a registration with that name, check!';
        $this->messages['pt'][] = 'Já existe um cadastro com esse nome, verifique!';
        $this->messages['es'][] = 'Ya existe un registro con ese nombre, ¡comprueba!';

        $this->messages['en'][] = 'There is already a registration with this code, check it!';
        $this->messages['pt'][] = 'Já existe um cadastro com esse código, verifique!';
        $this->messages['es'][] = 'Ya existe un registro con este código, ¡compruébalo!';
        
        foreach ($this->messages as $lang => $messages)
        {
            $this->sourceMessages[$lang] = array_flip( $this->messages[ $lang ] );
        }
    }
    
    /**
     * Returns the singleton instance
     * @return  Instance of self
     */
    public static function getInstance()
    {
        // if there's no instance
        if (empty(self::$instance))
        {
            // creates a new object
            self::$instance = new self;
        }
        // returns the created instance
        return self::$instance;
    }
    
    /**
     * Define the target language
     * @param $lang Target language index
     */
    public static function setLanguage($lang, $global = true)
    {
        $instance = self::getInstance();
        
        if (substr( (string) $lang,0,4) == 'auto')
        {
            $parts = explode(',', $lang);
            $lang = $parts[1];
            
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                $autolang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
                if (in_array($autolang, array_keys($instance->messages)))
                {
                    $lang = $autolang;
                }
            }
        }
        
        if (in_array($lang, array_keys($instance->messages)))
        {
            $instance->lang = $lang;
        }
        
        if ($global)
        {
            AdiantiCoreTranslator::setLanguage( $lang );
            AdiantiTemplateTranslator::setLanguage( $lang );
        }
    }
    
    /**
     * Returns the target language
     * @return Target language index
     */
    public static function getLanguage()
    {
        $instance = self::getInstance();
        return $instance->lang;
    }
    
    /**
     * Translate a word to the target language
     * @param $word     Word to be translated
     * @return          Translated word
     */
    public static function translate($word, $source_language, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
    {
        // get the self unique instance
        $instance = self::getInstance();
        // search by the numeric index of the word
        
        if (isset($instance->sourceMessages[$source_language][$word]) and !is_null($instance->sourceMessages[$source_language][$word]))
        {
            $key = $instance->sourceMessages[$source_language][$word];
            
            // get the target language
            $language = self::getLanguage();
            
            // returns the translated word
            $message = $instance->messages[$language][$key];
            
            if (isset($param1))
            {
                $message = str_replace('^1', $param1, $message);
            }
            if (isset($param2))
            {
                $message = str_replace('^2', $param2, $message);
            }
            if (isset($param3))
            {
                $message = str_replace('^3', $param3, $message);
            }
            if (isset($param4))
            {
                $message = str_replace('^4', $param4, $message);
            }
            return $message;
        }
        else
        {
            $word_template = AdiantiTemplateTranslator::translate($word, $source_language, $param1, $param2, $param3, $param4);
            
            if ($word_template)
            {
                return $word_template;
            }
            
            return 'Message not found: '. $word;
        }
    }
    
    /**
     * Translate a template file
     */
    public static function translateTemplate($template)
    {
        // search by translated words
        if(preg_match_all( '!_t\{(.*?)\}!i', $template, $match ) > 0)
        {
            foreach($match[1] as $word)
            {
                $translated = _t($word);
                $template = str_replace('_t{'.$word.'}', $translated, $template);
            }
        }
        
        if(preg_match_all( '!_tf\{(.*?), (.*?)\}!i', $template, $matches ) > 0)
        {
            foreach($matches[0] as $key => $match)
            {
                $raw        = $matches[0][$key];
                $word       = $matches[1][$key];
                $from       = $matches[2][$key];
                $translated = _tf($word, $from);
                $template = str_replace($raw, $translated, $template);
            }
        }
        return $template;
    }
}

/**
 * Facade to translate words from english
 * @param $word  Word to be translated
 * @param $param1 optional ^1
 * @param $param2 optional ^2
 * @param $param3 optional ^3
 * @return Translated word
 */
function _t($msg, $param1 = null, $param2 = null, $param3 = null)
{
    return ApplicationTranslator::translate($msg, 'en', $param1, $param2, $param3);
}

/**
 * Facade to translate words from specified language
 * @param $word  Word to be translated
 * @param $source_language  Source language
 * @param $param1 optional ^1
 * @param $param2 optional ^2
 * @param $param3 optional ^3
 * @return Translated word
 */
function _tf($msg, $source_language = 'en', $param1 = null, $param2 = null, $param3 = null)
{
    return ApplicationTranslator::translate($msg, $source_language, $param1, $param2, $param3);
}
