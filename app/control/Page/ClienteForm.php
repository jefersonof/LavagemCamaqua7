<?php

//<fileHeader>

//</fileHeader>

class ClienteForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'lavagem';// samples
    private static $activeRecord = 'Customer';
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

        $id          = new THidden('id');
        $nome        = new TEntry('name');
        $veiculo     = new TEntry('car');
        $placa       = new TEntry('plate');
        $phone       = new TEntry('phone');
        $endereco    = new TEntry('address');
        $complement  = new TEntry('complement');
        $taxi_app    = new TDBRadioGroup('taxi_app', 'lavagem', 'Category', 'id', '{name}', 'id');;
        $date_create = new TDate('date_create');
      
        $id->setEditable(false);
        $taxi_app->setLayout('horizontal');
        $taxi_app->setValue(1);
        $options = [1 => 'Padrão', 2 => 'Tax ou App'];
        
        
        $placa->setMaxLength(8);
        $phone->setMaxLength(10);
        $nome->setMaxLength(255);
        $veiculo->setMaxLength(400);
        $endereco->setMaxLength(400);
        $complement->setMaxLength(255);

        $id->setSize(100);
        $nome->setSize('100%');
        $placa->setSize('100%');
        $veiculo->setSize('100%');
        $endereco->setSize('100%');
        $complement->setSize('100%');
        $phone->setSize('100%');
        $phone->setMask('99999-9999');

        //<onBeforeAddFieldsToForm>

        //</onBeforeAddFieldsToForm>
        $this->form->addFields([$id]);//Hidden
        
        $row1 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Telefone:", null, '14px', null, '100%'),$phone]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Veiculo:", null, '14px', null, '100%'),$veiculo],[new TLabel("Placa do Veículo:", null, '14px', null, '100%'),$placa]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Endereco:", null, '14px', null, '100%'),$endereco],[new TLabel("Complemento:", null, '14px', null, '100%'),$complement]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Tipo de Cliente:", null, '14px', null, '100%'),$taxi_app]);
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

            $object = new Customer(); // create an empty object //</blockLine>
            
            $data = $this->form->getData(); // get form data as array

            $object->fromArray( (array) $data); // load the object with data

            //</beforeStoreAutoCode> //</blockLine>

            //TApplication::loadPage('OrdemDeServicoForm1', 'onOrdem');
            //TApplication::loadPage('ClienteList', 'onClear', $loadPageParam);

            if(empty($object->date_create))
            {
                $object->date_create = date('Y/m/d');
            }
            
            $object->store(); // save the object //</blockLine>

            //TApplication::loadPage('OrdemDeServicoForm1', 'onOrdem');
            TApplication::loadPage('ClienteList', 'onClear');
            

            //</afterStoreAutoCode> //</blockLine>
 //<generatedAutoCode>

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

//</generatedAutoCode>

            // get the generated {PRIMARY_KEY}
            //</blockLine>

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

                $object = new Customer($key); // instantiates the Active Record //</blockLine>

                //</beforeSetDataAutoCode> //</blockLine>

                $this->form->setData($object); // fill the form //</blockLine>

                //</afterSetDataAutoCode> //</blockLine>
                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();

                $data->
                $this->form->setData($data);
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