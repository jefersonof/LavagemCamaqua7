<?php
class ClienteList Extends TPage
{
    private $form;
	private $datagrid;
	private $pageNavigation;

    
	
	public function __construct()
	{
		parent::__construct();
		
		//cria o form
		$this->form = new BootstrapFormBuilder('Lista de Clientes');//BootstrapFormBuilder
		$this->form->setFieldSizes('100%');
		$this->form->class = 'tform';
		
		//cria os atributos
		$nome = new TEntry('nome');//tipocto
		$nome->setValue(TSession::getValue('TS_nome'));//tipocto

         //recupera a sessão
        $placa = new TEntry('placa');//tipocto
		$placa->setValue(TSession::getValue('TS_placa'));//tipocto
        
		
		//cria os Btn
		$btn_fechar = TButton::create('btn_fechar', array('ClienteForm1', 'onEdit'), 'Cadastrar Cliente', 'fa:plus');
		
		$row = $this->form->addFields( [ new TLabel('Nome'), $nome ], [new TLabel('Placa'), $placa ]);
		$row->layout = ['col-sm-8','col-sm-4'];
		
		//cria as ações do form
		$this->form->addAction('Pesquisar', new TAction(array($this, 'onSearch')), 'fa:search' );
		
		$this->form->addAction ('Cadastrar Cliente', new TAction(array('ClienteForm1', 'onEdit')), 'fa: fa-power-off red');
		
		//cria o datagrid
		$this->datagrid = new TQuickGrid;
		$this->datagrid->style = 'width:100%';
		//$this->datagrid->makeScrollable(); 
		$this->datagrid->DisableDefaultClick(); //DisableDefaultClick
		//$this->datagrid->SetHeight(300);
		$this->datagrid->addQuickColumn('Código', 'id', 'center');
		$this->datagrid->addQuickColumn('Nome', 'nome', 'center', '30%');
		$this->datagrid->addQuickColumn('Placa', 'placa', 'center');
		$this->datagrid->addQuickColumn('Telefone', 'telefone', 'center');
		
		//ações da grid 'Excluir' / 'Editar'
		$this->datagrid->addQuickAction('Editar' ,new TDataGridAction(array('ClienteForm', 'onEdit')), 'id', 'fa:edit blue');
		
		//if($permissao_geral['delecao'] == 1)
		//{
		$this->datagrid->addQuickAction('Excluir' ,new TDataGridAction(array($this, 'onDelete')), 'id', 'far:trash-alt red' );	
		
		$this->datagrid->CreateModel();

        //informa os campos do form
		$this->formFields =  array($nome, $placa);
		
		//add os campos no form
		$this->form->setFields($this->formFields);
		
		//cria o paginador
		$this->pageNavigation = new TPageNavigation();
		$this->pageNavigation->enableCounters(); 
		$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
		$this->pageNavigation->setWidth($this->datagrid->getWidth());
		
		//empacotamento
		$panelGroup = new TPanelGroup('Lista de Cliente');
		
		$panelGroup->add($this->form);
		$panelGroup->add($this->datagrid);
		$panelGroup->add($this->pageNavigation);
		
		//rodape da pagina
		$panelGroup->addFooter(THBox::pack());
		
		//ativar a rolagem horizontal dentro do corpo do painel
        $panelGroup->getBody()->style = "overflow-x:auto;";
		
		
		//add o painel em tela
		$menuBread = new TXMLBreadCrumb('menu.xml', __CLASS__);
		//$menuBread->style = 'margin:0 0 0 30px';	
		
		$vbox = new TVBox;
		$vbox->style = 'width:90%';
        $vbox->add($menuBread);
        $vbox->add($panelGroup);
		
        parent::add($vbox);
		
		
	}//__construct
	
	/*
	Atualiza a página com os parâmetros atuais
	*/
	public function onReload($param)
	{
		try
		{
			TTransaction::open('lavagem');
			
			//$data = $this->form->getData();
			
			$rp_cobertura = new TRepository('Cliente');
			$criteria = new TCriteria;
			
			//set as propriedades
			//$criteria->setProperty('order','NOME');//NOME
			$criteria->setProperty('order','id');//NOME
			$criteria->setProperty('direction','ASC');
			$criteria->setProperty('limit',2);

            $criteria->setProperties($param);
			
			if(TSession::getValue('TS_filter_nome'))
			{
				$criteria->add(TSession::getValue('TS_filter_nome'));
			}
			
			if(TSession::getValue('TS_filter_placa'))
			{
				$criteria->add(TSession::getValue('TS_filter_placa'));
			}
			

			$cobertura =  $rp_cobertura->load($criteria);	
			
			$this->datagrid->clear();
			foreach($cobertura as $coberturas)
			{
				//$coberturas->ENT_GAR = $coberturas->entgarantidora->NOME;
				
				$this->datagrid->additem($coberturas);
			}
			
			$criteria->resetProperties();
			$count = $rp_cobertura->count( $criteria ); 

            $this->pageNavigation->setCount ( $count );
            $this->pageNavigation->setProperties ( $param );
            $this->pageNavigation->setlimit(2);
			
			
			//$this->form->setData($data);
			
			TTransaction::close();
			
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage() );
			TTransaction::rollback();
		}
	}//onReload
	
	
	/*
	Grava os filtros de busca na sessão e chama o onReload()
	*/
	public function onSearch($param)
	{
        $data = $this->form->getData();
        
        if($data->nome)
        {
            $filter = new TFilter('nome', 'like', "%$data->nome%");
            TSession::setValue('TS_filter_nome', $filter);
            TSession::setValue('TS_nome', $data->nome);
        }
        else
        {
            TSession::setValue('TS_filter_nome', NULL);
        }	
        
        if($data->placa)
        {
            $filter = new TFilter('placa', 'like', "$data->placa");
            TSession::setValue('TS_filter_placa', $filter);//
            TSession::setValue('TS_placa', $data->placa);
        }
        else
        {
            TSession::setValue('TS_filter_placa', NULL);
        }		
        
        $param = array();
        $param['offset'] = 0;
        $param['first_page'] = 1;
        
        $this->onReload($param);	
        
        $this->form->setData($data);
		
	}//onSearch

    /*
	  Questiona a exclusão de uma 'cobertura'
	*/
	public function onDelete($param)
	{
		try
		{
			TTransaction::open('lavagem');
			
			$key = $param['key'];
			$cobertura =  new cobertura($key);
			$cobNome =  $cobertura->COBERTURA;
			
			$onSimDelete = new TAction( array($this ,'onSimDelete'));
			$onSimDelete->setParameter('id', $key);
			
			//$ac_onSim->setParameter('ID_PLANOS_SUSEP', $key);
			
			new TQuestion('Deseja apagar '. '"' . $cobNome . '"' , $onSimDelete);
			
			TTransaction::close();
		}
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage() );
			TTransaction::rollback();
		}
		
	}//onDelete
	
	/*
	Exclui uma 'cobertura'
	*/
	public function onSimDelete($param)
	{
		try
		{
			TTransaction::open('lavagem');
			
			$key = $param['id'];
			$rp_cobertura = new TRepository('cliente');
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', $key));
			
			$rp_cobertura->delete($criteria);
			
			//new TMessage('indo', 'Registro apagado');
			
			TTransaction::close();
			
			$this->onReload($param);
			
			
		}
		catch(Excepition $e)
		{
			new TMessage('error', $e->getMessage() );
			TTransaction::rollback();
		}
		
	}//onSimDelete
	
	/*
	captura as parametros da URL e atualiza o onReload
	*/
	public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
	
	/*
	Limpa o form e as variaveis de sessão
	*/
	public function onClear()
	{
		$data = $this->form->getData();
		$this->datagrid->clear();
		//$this->form->clear();
		
		//TSession::setValue('TS_tipocto', NUll);
		//TSession::setValue('TS_tipocto', array() );
		
		TSession::setValue('TS_filter_a', NUll);
		TSession::setValue('TS_filter_s', NUll);
		TSession::setValue('TS_filter_p', NUll);
		
		$this->form->setData($data);
		
	}//onClear	
	
	
	
		
	
	/*
	FILTRO DELPHI
	Coberturas List
	  begin
	  if ckTipoCto.Checked = true then
	  begin
		dmdbx.sdsCob.Filter := 'TIPO ='+ QuotedStr(dmdbx.sdsParametrosMOD_ARQ.AsString);
		dmdbx.sdsCob.Filtered := true;
	  end
	  else
	  begin
		dmdbx.sdsCob.Filtered := false;
	*/
	
	//https://www.youtube.com/watch?v=hwulmocF1GQ
	
}//TPage


?>
