<?php
/**
 * SaleForm Registration
 * @author  <your name here>
 */
class OrdemDeServicoForm1 extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        $this->setTargetContainer('adianti_right_panel');
        
        /*parent::setSize(0.8, null);
        parent::removePadding();
        parent::removeTitleBar();
        parent::disableEscape();
        */
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Sale');
        $this->form->setFormTitle('Ordem de Serviço');
        $this->form->setProperty('style', 'margin:0;border:0');
        $this->form->setClientValidation(true);
        
        // master fields
        $id          = new TEntry('id');
        $placa       = new TEntry('plate');
        $veiculo     = new TEntry('car');
        $date        = new TDate('date');
        //$customer_id = new TDBUniqueSearch('customer_id', 'samples', 'Customer', 'id', 'name');
        $cliente_id  = new THidden('cliente_id');
        $customer_id = new TEntry('name');
        $phone       = new TEntry('phone');
        $obs         = new TText('obs');
        
        $button = new TActionLink('', new TAction(['ClienteForm', 'onEdit']), 'green', null, null, 'fa:plus-circle');
        $button->class = 'btn btn-default inline-button';
        $button->title = _t('New');
        //$customer_id->after($button);

        $btn_salvar   = TButton::create('btn_salvar', array($this,'onSave'), 'Salvar','far:save');
		$btn_salvar->class = 'btn btn-sm  btn-primary';//fa:floppy-o *onSave * onTeste
        
        // detail fields
        $product_detail_unqid      = new THidden('product_detail_uniqid');
        $product_detail_id         = new THidden('product_detail_id');
        $product_detail_product_id = new TDBUniqueSearch('product_detail_product_id', 'lavagem', 'Product', 'id', 'description');//'db_unique', 'samples', 'Product', 'sale_price', 'description'
        $product_detail_price      = new TEntry('product_detail_price');
        $product_detail_amount     = new TEntry('product_detail_amount');
        $product_detail_discount   = new TEntry('product_detail_discount');
        $product_detail_total      = new TEntry('product_detail_total');
        
        // adjust field properties
        $id->setEditable(false);
        $customer_id->setEditable(false);
        $phone->setEditable(false);
        $veiculo->setEditable(false);
        $placa->setEditable(false);
        $date->setEditable(false);
        //$customer_id->setSize('100%');
        $id->setSize('calc(70%)');
        $customer_id->setSize('calc(120%)');
        $veiculo->setSize('calc(120%)');
        $phone->setSize('calc(80% - 10px)');
        //$customer_id->setMinLength(1);
        $date->setSize('100%');
        $obs->setSize('100%', 80);
        $product_detail_product_id->setSize('100%');
        $product_detail_product_id->setMinLength(1);
        $date->setMask('dd/mm/yyyy');
        $date->setDataBaseMask('yyyy/mm/dd');
        $product_detail_product_id->setMask(' {description}  -  ({sale_price}) ');
        $product_detail_price->setSize('100%');
        $product_detail_amount->setSize('100%');
        $product_detail_discount->setSize('100%');
        
        // add validations
        //$date->addValidation('Date', new TRequiredValidator);
        $customer_id->addValidation('Customer', new TRequiredValidator);
        
        // change action
        $product_detail_product_id->setChangeAction(new TAction([$this,'onProductChange']));
        
        // add master form fields
        $this->form->addFields( [], [$cliente_id]);
        $this->form->addFields( [new TLabel('ID')], [$id], 
                                [new TLabel('Data ')], [$date] );
        $this->form->addFields( [new TLabel('Nome')], [$customer_id ],
                                [new TLabel('Telefone')], [$phone ], );
        $this->form->addFields( [new TLabel('Veículo')], [$veiculo], 
                                [new TLabel('Placa')], [$placa] );
        $this->form->addFields( [new TLabel('Obs')], [$obs] );
        
        $this->form->addContent( ['<h5>Detalhes do Serviço</h5><hr>'] );
        $this->form->addFields( [ $product_detail_unqid], [$product_detail_id] );
        $this->form->addFields( [ new TLabel('Serviço') ], [$product_detail_product_id]);
        $this->form->addFields( [ new TLabel('Valor') ],   [$product_detail_price],
                                [ new TLabel('Desconto')], [$product_detail_discount] );
        
        $add_product = TButton::create('add_product', [$this, 'onProductAdd'], 'Adicionar', 'fa:plus-circle green');
        $add_product->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add_product] );
        
        $this->product_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->product_list->setHeight(90);
        $this->product_list->makeScrollable();
        $this->product_list->setId('products_list');
        $this->product_list->generateHiddenFields();
        $this->product_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->product_list->setMutationAction(new TAction([$this, 'onMutationAction']));
        
        $col_uniq   = new TDataGridColumn( 'uniqid', 'Uniqid', 'center', '10%');
        $col_id     = new TDataGridColumn( 'id', 'ID', 'center', '10%');
        $col_pid    = new TDataGridColumn( 'product_id', 'Código', 'center', '10%');
        $col_descr  = new TDataGridColumn( 'product_id', 'Serviço', 'left', '40%');
        $col_price  = new TDataGridColumn( 'sale_price', 'Valor', 'right', '15%');
        $col_disc   = new TDataGridColumn( 'discount', 'Desconto', 'right', '15%');
        $col_subt   = new TDataGridColumn( '=( {sale_price} - {discount} )', 'Total', 'right', '20%');
        
        $this->product_list->addColumn( $col_uniq );
        $this->product_list->addColumn( $col_id );
        $this->product_list->addColumn( $col_pid );
        $this->product_list->addColumn( $col_descr );
        $this->product_list->addColumn( $col_price );
        $this->product_list->addColumn( $col_disc );
        $this->product_list->addColumn( $col_subt );
        
        $col_descr->setTransformer(function($value) {
            return Product::findInTransaction('lavagem', $value)->description;
        });
        
        $col_subt->enableTotal('sum', 'R$', 2, ',', '.');
        
        $col_id->setVisibility(false);
        $col_uniq->setVisibility(false);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEditItemProduto'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDeleteItem']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->product_list->addAction($action1, _t('Edit'), 'far:edit blue');
        $this->product_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->product_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->product_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $col_price->setTransformer( $format_value );
        $col_disc->setTransformer( $format_value );
        $col_subt->setTransformer( $format_value );
        
        $this->form->addHeaderActionLink( _t('Close'),  new TAction([__CLASS__, 'onClose'], ['static'=>'1']), 'fa:times red');

        $this->formFields = array($btn_salvar, $add_product, $product_detail_unqid, $product_detail_id, $product_detail_product_id, $product_detail_price, $product_detail_discount, $placa, $veiculo, $date, $customer_id, $cliente_id, $phone, $obs); //, $numero_parcela, $valor_parc

        $this->form->setFields( $this->formFields );
        
        //painel
		$painel = new TPanelGroup();
		$painel->addFooter(THBox::pack($btn_salvar));
		$painel->add($this->form);
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($painel);
        parent::add($container);
    }

     /**
     * Clear form
     * @param $param URL parameters
     */
    function onOrdem($param)
    {
        TTransaction::open('lavagem');

        $this->form->clear();
        $data = $this->form->getData();

        $id_last = TSession::getValue('TS_idLast');

        $key = $param['key'];
        
        $cliente = new Customer($param['key']);

        $data->cliente_id  = $cliente->id;
        $data->customer_id = $cliente->id;
        $data->name        = $cliente->name;
        $data->car         = $cliente->car;
        $data->plate       = $cliente->plate;
        $data->phone       = $cliente->phone;
        $data->date        = date('d/m/Y'); 

        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );

        TTransaction::close();

    }
    
    /**
     * Pre load some data
     */
    public function onLoad($param)
    {
        $data = new stdClass;
        $data->customer_id   = $param['customer_id'];
        $this->form->setData($data);
    }
    
    
    /**
     * On product change
     */
    public static function onProductChange( $params )
    {
        if( !empty($params['product_detail_product_id']) )
        {
            try
            {
                TTransaction::open('lavagem');
                $product   = new Product($params['product_detail_product_id']);
                TForm::sendData('form_Sale', (object) ['product_detail_price' => $product->sale_price ]);
                TTransaction::close();
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    function onClear($param)
    {
        $this->form->clear();
    }

    
    /**
     * Add a product into item list
     * @param $param URL parameters
     */
    public function onProductAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            if( (! $data->product_detail_product_id) || (! $data->product_detail_price) )
            {
                throw new Exception('The fields Product, Amount and Price are required');
            }
            
            $uniqid = !empty($data->product_detail_uniqid) ? $data->product_detail_uniqid : uniqid();
            
            $grid_data = ['uniqid'      => $uniqid,
                          'id'          => $data->product_detail_id,
                          'product_id'  => $data->product_detail_product_id,
                          'sale_price'  => $data->product_detail_price,
                          'discount'    => $data->product_detail_discount];
            
            // insert row dynamically
            $row = $this->product_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('products_list', $uniqid, $row);
            
            // clear product form fields after add
            $data->product_detail_uniqid     = '';
            $data->product_detail_id         = '';
            $data->product_detail_product_id = '';
            $data->product_detail_name       = '';
            $data->product_detail_price      = '';
            $data->product_detail_discount   = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Sale', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit a product from item list
     * @param $param URL parameters
     */
    public static function onEditItemProduto( $param )
    {
        $data = new stdClass;
        $data->product_detail_uniqid     = $param['uniqid'];
        $data->product_detail_id         = $param['id'];
        $data->product_detail_product_id = $param['product_id'];
        $data->product_detail_price      = $param['sale_price'];
        $data->product_detail_discount   = $param['discount'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
    }
    
    /**
     * Delete a product from item list
     * @param $param URL parameters
     */
    public static function onDeleteItem( $param )
    {


        $data = new stdClass;
        $data->product_detail_uniqid     = '';
        $data->product_detail_id         = '';
        $data->product_detail_product_id = '';
        $data->product_detail_price      = '';
        $data->product_detail_discount   = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Sale', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('products_list', $param['uniqid']);
    }
    
    /**
     * Edit Sale
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('lavagem');
            
            if (isset($param['key']))
            {
                $key = $param['key'];

                
                $object = new Sale($key);

                var_dump($object->status->id);
                //exit;

                //Desabilita o Editar depois que o serviço já foi finalizado
                if($object->status->id == 2)
                {   
                    TButton::disableField('sale_id', 'btn_salvar');
                }
                
                $sale_items = SaleItem::where('sale_id', '=', $object->id)->load();
                
                foreach( $sale_items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->product_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                //pegar os dados do cliente
                $this->form->setData($object);

                //Atualiza os dados do formulário
                $dadosFom = new StdClass;
                $dadosFom->id    = $object->id;
                $dadosFom->name  = $object->customer->name;
                $dadosFom->phone = $object->customer->phone;
                $dadosFom->car   = $object->customer->car;
                $dadosFom->plate = $object->customer->plate;

                //ADICIONA EM TELA AS VALORES
		        TForm::sendData('form_Sale', $dadosFom);

                TTransaction::close();
            }
            else
            {
                $this->form->clear();
                
                //Abilita o botão salvar
                TSession::setValue('TS_editar', 'yes');
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the sale and the sale items
     */
    public function onSave($param)
    {
        try
        {
            TTransaction::open('lavagem');
           
            $data = $this->form->getData();
            $this->form->validate();
            
            $sale = new Sale;
            $sale->fromArray((array) $data);

            $sale->customer_id =  $data->cliente_id;
            $sale->status_id   =  1;
            $sale->store();
            
            SaleItem::where('sale_id', '=', $sale->id)->delete();
            
            $total = 0;
            if( !empty($param['products_list_product_id'] ))
            {
                foreach( $param['products_list_product_id'] as $key => $item_id )
                {
                    $item = new SaleItem;
                    $item->product_id  = $item_id;
                    $item->sale_price  = (float) $param['products_list_sale_price'][$key];
                    $item->discount    = (float) $param['products_list_discount'][$key];
                    $item->total       =  $item->sale_price - $item->discount;
                    
                    $item->sale_id = $sale->id;
                    $item->store();
                    $total += $item->total;
                }
            }
            $sale->total = $total;
            $sale->store(); // stores the object
            
            TForm::sendData('form_Sale', (object) ['id' => $sale->id]);
            
            TTransaction::close(); // close the transaction
            //new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ClienteList', 'onClear');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     *
     */
    public static function onMutationAction($param)
    {
        // Form data: $param['form_data']
        // List data: $param['list_data']
        //echo '<pre>';var_dump($param);
        $total = 0;
        
        if(isset($param['list_data']))
        {
            foreach ($param['list_data'] as $row)
            {
                $total +=  floatval($row['sale_price']) - floatval($row['discount']);
            }
        }
        
        TToast::show('info', 'Novo total: <b>' . 'R$ '.number_format($total, 2, ',', '.') . '</b>', 'bottom right');
    }
    
    /**
     * Closes window
     */
    public static function onClose()
    {
        TScript::create("Template.closeRightPanel()");
    }
}

 
