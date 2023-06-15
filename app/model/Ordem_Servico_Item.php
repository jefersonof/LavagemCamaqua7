<?php

//<fileHeader>
  
//</fileHeader>

class Ordem_Servico_Item extends TRecord
{
    const TABLENAME  = 'ordem_servico_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('tipo_servico_id');//FK Cliente
        parent::addAttribute('ordem_servico_id');//FK Tipo Servico
        parent::addAttribute('valor');
        parent::addAttribute('desconto');
        parent::addAttribute('total');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
    
}

