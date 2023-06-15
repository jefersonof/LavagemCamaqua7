<?php
/**
 * FormVerticalBuilderView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ClienteForm1 extends TPage
{
    private $form;
    
    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_builder');
        $this->form->setFormTitle('Cadastro de Cliente');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label
        $this->form->appendPage('Dados Básicos');
        
        $id         = new TEntry('id');
        $name         = new TEntry('nome');
        $veiculo      = new TEntry('veiculo');
        $placa        = new TEntry('placa');
        $endereco     = new TEntry('endereco');
        $numero       = new TEntry('numero');
        $complemento  = new TEntry('complemento');
        $telefone     = new TEntry('telefone');
      
        $telefone->setMask('9999-99999');
        $id->setEditable(FALSE);
        
        $row = $this->form->addFields( [ new TLabel('Id'),   $id ],
                                       [ new TLabel('Name'),     $name ],
                                       [ new TLabel('Telefone'),   $telefone ] );
        $row->layout = ['col-sm-2', 'col-sm-8', 'col-sm-2' ];

        $row = $this->form->addFields( [ new TLabel('Veículo'),     $veiculo ],
                                       [ new TLabel('Placa'),     $placa ] );
        $row->layout = ['col-sm-8', 'col-sm-4' ];

        $row = $this->form->addFields( [ new TLabel('Endereço'),     $endereco ],
                                       [ new TLabel('Número'),     $numero ] );
        $row->layout = ['col-sm-9', 'col-sm-3' ];

        $row = $this->form->addFields( [ new TLabel('Complemento'),     $complemento ]);
        $row->layout = ['col-sm-12'];
        
        //Btn salvar
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary');
        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('lavagem');
			
                $this->form->validate();
                
                $cliente =  $this->form->getData('Cliente');
                
                $cliente->store();
                
                $this->form->setData($cliente);
                
                //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            //TApplication::loadPage('ClienteHeaderList', 'onShow', $loadPageParam);
//</generatedAutoCode>
                
                
                //Desabilita o 'CODIGO'
                //TEntry::disableField('formBancos', 'CODIGO');
			
			TTransaction::close();
       

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine>

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    //<generated-onEdit>
    public function onEdit( $param )//</ini>
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Cliente($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }//</end>
//</generated-onEdit>

 /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }
    
    /**
     * Post data
     */
    public function onSend($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data);
        
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
