<?php

//<fileHeader>

//</fileHeader>

class ClienteForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'lavagem';
    private static $activeRecord = 'Cliente';
    private static $primaryKey = 'id';
    private static $formName = 'form_ClienteForm';

    //<classProperties>

    //</classProperties>

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de cliente");

        //<onBeginPageCreation>

        //</onBeginPageCreation>

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $veiculo = new TEntry('veiculo');
        $placa = new TEntry('placa');
        $endereco = new TEntry('endereco');
        $numero = new TEntry('numero');
        $complento = new TEntry('complento');
      
        $id->setEditable(false);
        
        $placa->setMaxLength(8);
        $nome->setMaxLength(255);
        $numero->setMaxLength(10);
        $veiculo->setMaxLength(400);
        $endereco->setMaxLength(400);
        $complento->setMaxLength(255);

        $id->setSize(100);
        $nome->setSize('100%');
        $placa->setSize('100%');
        $numero->setSize('100%');
        $veiculo->setSize('100%');
        $endereco->setSize('100%');
        $complento->setSize('100%');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Nome:", null, '14px', null, '100%'),$nome]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Veiculo:", null, '14px', null, '100%'),$veiculo],[new TLabel("Placa do Veículo:", null, '14px', null, '100%'),$placa]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Endereco:", null, '14px', null, '100%'),$endereco],[new TLabel("Numero:", null, '14px', null, '100%'),$numero]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Complento:", null, '14px', null, '100%'),$complento]);
        $row4->layout = ['col-sm-12'];

        //<onAfterFieldsCreation>

        //</onAfterFieldsCreation>

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        //<onAfterPageCreation>

        //</onAfterPageCreation>

        parent::add($this->form);

    }

//<generated-FormAction-onSave>
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Cliente(); // create an empty object //</blockLine>

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            //</beforeStoreAutoCode> //</blockLine>

            TApplication::loadPage('ClienteHeaderList', 'onShow', $loadPageParam);

            $object->store(); // save the object //</blockLine>
            

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; //</blockLine>

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            //</messageAutoCode> //</blockLine>
//<generatedAutoCode>
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            //TApplication::loadPage('ClienteHeaderList', 'onShow', $loadPageParam);
//</generatedAutoCode>

            //</endTryAutoCode> //</blockLine>
//<generatedAutoCode>
            TScript::create("Template.closeRightPanel();");
//</generatedAutoCode>

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> //</blockLine>

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
//</generated-FormAction-onSave>

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

    public function onShow($param = null)
    {

        //<onShow>

        //</onShow>
    } 

    //</hideLine> <addUserFunctionsCode/>

    //<userCustomFunctions>

    //</userCustomFunctions>

}