<?php

//<fileHeader>
  
//</fileHeader>

class Tipo_Servico extends TRecord
{
    const TABLENAME  = 'tipo_servico';
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
        parent::addAttribute('cliente_id');//Fk Cliente
        parent::addAttribute('tipo_servico_id');
        parent::addAttribute('valor');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
    
}

