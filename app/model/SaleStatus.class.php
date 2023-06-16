<?php
/**
 * Sale Active Record
 * @author  <your-name-here>
 */
class SaleStatus extends TRecord
{
    const TABLENAME = 'sale_status';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('color');
    }
}