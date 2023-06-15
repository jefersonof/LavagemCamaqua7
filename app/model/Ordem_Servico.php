<?php

//<fileHeader>
  
//</fileHeader>

class Ordem_Servico extends TRecord
{
    const TABLENAME  = 'ordem_servico';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}
    
    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'update_at';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('cliente_id');//FK Cliente
        parent::addAttribute('tipo_servico_id');//FK Tipo Servico
        parent::addAttribute('valor');
        parent::addAttribute('obs');
        parent::addAttribute('created_at');
        parent::addAttribute('update_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
    
}

