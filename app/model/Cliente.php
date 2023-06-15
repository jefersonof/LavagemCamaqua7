<?php

//<fileHeader>
  
//</fileHeader>

class Cliente extends TRecord
{
    const TABLENAME  = 'cliente';
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
        parent::addAttribute('nome');
        parent::addAttribute('veiculo');
        parent::addAttribute('placa');
        parent::addAttribute('endereco');
        parent::addAttribute('numero');
        parent::addAttribute('telefone');
        parent::addAttribute('complemento');
        parent::addAttribute('created_at');
        parent::addAttribute('update_at');
        parent::addAttribute('deleted_at');
        //<onAfterConstruct>
  
        //</onAfterConstruct>
    }
    
}

