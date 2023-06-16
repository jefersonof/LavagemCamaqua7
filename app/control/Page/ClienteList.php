<?php

class ClienteList extends TPage
{	
	private $form;
	private $datagrid;
	private $pageNavigation;

	public function __construct()
	{
		parent::__construct();
		
		//cria o form
		$this->form = new BootstrapFormBuilder('formCliente');
		$this->form->setFieldSizes('100%');
		//$this->form->style = 'width:100%'; 
		$this->form->class = 'tform'; 
		
		//cria os atributo
		$nome       = new TEntry('nome');
		$placa      = new TEntry('placa');

		//recupera a sessão
		$placa->setValue( TSession::getValue('TS_placa'));
		$nome->setValue( TSession::getValue('TS_nome')) ;

		//cria o botão
		$btn_fechar = TButton::create('btn_fechar', array($this, 'onReload'), 'Fechar', 'fa: fa-power-off red');			
		$btn_limpar = TButton::create('btn_limpar', array($this, 'onClear'), 'Limpar', 'fa:eraser red');
		
		//formatação
		$nome->setSize('100%');
		$placa->setSize('100%');

		//add os atributos dentro do form
		$row = $this->form->addfields( [ new  TLabel('Nome'), $nome ],
								       [ new TLabel('Placa'), $placa ]);
		$row->layout = ['col-sm-8', 'col-sm-4'];							   
		
		//add as ações do form
		$this->form->addAction('Pesquisar' ,new TAction(array($this, 'onSearch')), 'fa:search');
		$this->form->addAction('limpar' ,new TAction(array($this, 'onClear')), 'fa:eraser red');
		
		//cria a grid
		$this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
		$this->datagrid->style = 'width:100%';
		$this->datagrid->DisableDefaultClick();
		
		$this->datagrid->addQuickColumn('Id', 'id', 'center', '10%');
		$this->datagrid->addQuickColumn('Nome', 'name', 'center', '70%');
		$this->datagrid->addQuickColumn('Placa', 'placa', 'center', '20%');

		//cria as ações da grid
		$this->datagrid->addQuickAction('Ordem de serviço' ,new TDataGridAction(array('OrdemDeServicoForm1', 'onOrdem')), 'id', 'fas:shower' );//fa:edit blue

		$this->datagrid->addQuickAction('Editar' ,new TDataGridAction(array('ClienteForm', 'onEdit')), 'id', 'fa:edit blue' );//fa:edit blue
		
		$this->datagrid->addQuickAction('Excluir', new TDataGridAction(array($this, 'onDelete')), 'id', 'far:trash-alt red' );//far:trash-alt red
		
		$this->datagrid->createModel();
		
		//informa os campos do form
		$this->formFields =  array($nome, $placa, $btn_fechar, $btn_limpar);
		
		//add os campos no form
		$this->form->setFields($this->formFields);
		
		//pageNavigation
		$this->pageNavigation = new TPageNavigation();
		$this->pageNavigation->enableCounters();
		$this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
		$this->pageNavigation->setWidth($this->datagrid->getWidth());
		
		//Empacotamento
		$painel = new TPanelGroup('Lista de Clientes');
		
		$painel->add($this->form);
		$painel->add($this->datagrid);
		$painel->add($this->pageNavigation);
		
		//add os btn no footer da pagina
		$painel->addFooter(THBox::pack($btn_fechar, $btn_limpar ));
		
		// ativar a rolagem horizontal dentro do corpo do painel
        $painel->getBody()->style = "overflow-x:auto;";
		
		//add o painel em tela
		$menuBread = new TXMLBreadCrumb('menu.xml', __CLASS__);
		
		$vbox = new TVBox;
		$vbox->style = 'width:90%';
        $vbox->add($menuBread);
        $vbox->add($painel);
        //$vbox->add($this->pageNavigation);

        parent::add($vbox);		
	}//__construct

	/*
	Atualiza a página com os parâmetros atuais
	*/
	public function onReload($param)
	{
		try
		{
			TTransaction::open('samples');//db//db2//
			
			//var_dump(TSession::getValue('TS_cliente2'));
			$rp_cliente = new TRepository('Customer');
			
			$criteria = new TCriteria;
			
			//set as propriedades
			//$criteria->setProperty('order','NOME');//NOME
			$criteria->setProperty('order','id');//NOME
			$criteria->setProperty('direction','ASC');
			$criteria->setProperty('limit',5);
			
			$criteria->setProperties($param);
			
			if(TSession::getValue('TS_localiza_nome') ) 
			{	
				$criteria->add(TSession::getValue('TS_localiza_nome'));

			}//TS_localiza_nome
			
			if(TSession::getValue('TS_localiza_placa') ) 
			{	
				$criteria->add(TSession::getValue('TS_localiza_placa'));

			}//TS_localiza_nome
			
			$obj_cliente = $rp_cliente->load($criteria);
			
			TSession::setValue('TS_cliente', $obj_cliente);
			
			$this->datagrid->Clear();
			if($obj_cliente)
			{
				foreach($obj_cliente as $obj_clientes)
				{
					//$obj_clientes->nome = utf8_encode($obj_clientes->NOME);
					$this->datagrid->addItem($obj_clientes);
					
				}//foreach
				
			}//obj_cliente
			
			
			$criteria->resetProperties();
			$count = $rp_cliente->count( $criteria ); 

            $this->pageNavigation->setCount ( $count );
            $this->pageNavigation->setProperties ( $param );
            $this->pageNavigation->setlimit(5);
			
			TTransaction::close();
			//$this->form->setData($data);
			
		}//try
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage() );
			TTransaction::rollback();
		}
		
	}//onReload	
	
	public function onTeste($param)
	{
		$partes = explode("-", $param['nome_cliente']);
		$antes_barra  =  $partes[0]; 	
		$depois_barra =  $partes[1]; 
		
		echo '<pre>';
			var_dump($antes_barra) . '<br>';
			var_dump($depois_barra);
		echo '</pre>';
		
		echo '<pre>';
			var_dump($param['nome_cliente']);
		echo '</pre>';
		
	}//onTeste
	
	public function onTeste2($param)
	{	
		echo '<pre>';
			var_dump($param['CPF']);
		echo '</pre>';
		
	}//onTeste
	
	/*
	Questiona a exclusão de um 'cliente'
	*/
	public function onDelete($param)
	{
		TTransaction::open('lavagem');//db2
		
		$key = $param['key'];
		$cliente = new cliente($key);
		
		$nome = $cliente->NOME;
		
		$onsim = new TAction(array($this, 'onSimDelete'));
		$onsim->setParameter('id', $key );
		
		new TQuestion('Deseja apagar o cliente ' . ' " ' . $nome . ' " ', $onsim );
		
		TTransaction::close();
		
	}//onDelete
	
	/*
	exclui um 'cliente'
	*/
	public function onSimDelete($param)
	{
		try
		{
			TTransaction::open('lavagem');
			
			$rp_cliente = new TRepository('cliente');
			
			$criteria = new TCriteria;
			$criteria->add(new TFilter('id', '=', $param['id'] ));
			
			$rp_cliente->delete($criteria);
			
			TTransaction::close();
			
			$this->onReload($param);
			
			new TMessage('info', 'Cliente apagado' );
			
		}
		catch(Exception $e  )
		{
			TTransaction::rollback();
			new TMessage('error', $e->getMessage() );
		}
	}//onSimDelete
	
	
	/*
	Grava os filtros de busca na sessão e chama o onReload()
	*/
	public function onSearch($param)
	{
		try
		{
			$data = $this->form->getData();
			//$data = utf8_decode($data1);
			
			if($data->nome)
			{
				$filter	= new TFilter('name', 'LIKE', "%$data->nome%");
				TSession::setValue('TS_localiza_nome', $filter);
				TSession::setValue('TS_nome', $data->nome);
			}
			else
			{
				TSession::setValue('TS_localiza_nome', NULL);
				TSession::setValue('TS_nome', NULL);
			}//CPF
			
			if($data->placa)
			{
				$filter	= new TFilter('placa', 'LIKE', "%$data->placa%");
				TSession::setValue('TS_localiza_placa', $filter);
				TSession::setValue('TS_placa', $data->placa);
			}
			else
			{
				TSession::setValue('TS_localiza_placa', NULL);
				TSession::setValue('TS_placa', NULL);
			}//MATR_INTERNA

			/*if($data->NOME)
			{
				//$data->NOME = utf8_decode($data->NOME);
				$i = $data->NOME;
				$data->NOME = $this->onTiraAcentos($i);
				$filter	= new TFilter($this->onTiraAcentos('NOME'), 'LIKE', "%$data->NOME%");
				TSession::setValue('TS_localiza_nome', $filter);
				$data->NOME = utf8_encode($data->NOME);
				TSession::setValue('TS_relacao_nome', $data->NOME);
			}
			else
			{
				TSession::setValue('TS_localiza_nome', NULL);
				TSession::setValue('TS_relacao_nome', NULL);
			}//NOME*/
			
			$param = array();
			$param['offset'] = 0;
			$param['first_page'] = 1;
			$this->form->getdata();
			
			$this->onReload( $param );
			
			$this->form->setData($data);
			
			//habilita o btn 'Imprime lista' 
			//TButton::enableField('formCliente', 'btn_pdf');
			
		}//try
		catch(Exception $e)
		{
			new TMessage('error', $e->getMessage() );
			
			//limpa a datagrid
			$this->datagrid->clear();
		}
		
	}//onSearch
	
	
	public function onTiraAcentos($i)
	{
		return preg_replace(array("/(á|à|ã|â|ä|Á|À|Ã|Â|Ä)/","/(é|è|ê|ë|É|È|Ê|Ë)/","/(í|ì|î|ï|Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö|Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü|Ú|Ù|Û|Ü)/","/(ñ|Ñ)/","/(ç|Ç)/","/(ý|ÿ|Ý)/"),explode(" ","a e i o u n c y"),$i);
	}//onTiraAcentos
	
	/*
	Limpa o form e as variaveis de sessão
	*/
	public function onClear($param)
	{
		//Reseta as TEntry 		
		TSession::setValue('TS_nome', NULL);//TS_localiza_nome
		TSession::setValue('TS_placa', NULL);
		
		//Reseta os TFilter
		TSession::setValue('TS_localiza_nome', NULL);
		TSession::setValue('TS_localiza_placa', NULL);//TS_localiza_nome
		
		$this->form->clear();
		//$this->datagrid->clear() ;
		
		$this->onReload($param);
		//TButton::disableField('formAssossiado', 'btn_pdf');	
		
	}//onClear
	
	
	public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
	
	
}//Twindow

?>