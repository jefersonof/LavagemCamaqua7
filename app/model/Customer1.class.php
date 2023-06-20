<?php
/**
 * Customer Active Record
 * @author  Pablo Dall'Oglio
 */
class Customer1 extends TRecord
{
    const TABLENAME    = 'customer1';
    const PRIMARYKEY   = 'id';
    const IDPOLICY     =  'serial'; // {max, serial}
    const CACHECONTROL = 'TAPCache';
    
    const CREATEDAT = 'created_at';
    const UPDATEDAT = 'updated_at';
    const DELETEDAT = 'deleted_at';
    
    private $category;
    private $city;
    private $skills;
    private $contacts;

    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
      
        parent::addAttribute('car');
        parent::addAttribute('plate');
        parent::addAttribute('name');
        parent::addAttribute('address');
        parent::addAttribute('complement');
        parent::addAttribute('phone');
        parent::addAttribute('birthdate');
        parent::addAttribute('status');
        parent::addAttribute('email');
        parent::addAttribute('gender');
        parent::addAttribute('date_create');
    }

    
   
    
    /**
     * Returns the customer sales
     */
    public function getSales()
    {
        return Sale::getCustomerSales($this->id);
    }
    
    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->skills = array();
        $this->contacts = array();
    }

     /**
     * Method addContact
     * Add a Contact to the Customer
     * @param $object Instance of Contact
     */
    public function addContact(Contact $object)
    {
        $this->contacts[] = $object;
    }
    

     /**
     * Method getContacts
     * Return the Customer' Contact's
     * @return Collection of Contact
     */
    public function getContacts()
    {
        return $this->contacts;
    }
    
    /**
     * Method addSkill
     * Add a Skill to the Customer
     * @param $object Instance of Skill
     */
    public function addSkill(Skill $object)
    {
        $this->skills[] = $object;
    }
    
    /**
     * Method getSkills
     * Return the Customer' Skill's
     * @return Collection of Skill
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->skills = parent::loadAggregate('Skill', 'CustomerSkill', 'customer_id', 'skill_id', $id);
        $this->contacts = parent::loadComposite('Contact', 'customer_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveAggregate('CustomerSkill', 'customer_id', 'skill_id', $this->id, $this->skills);
        parent::saveComposite('Contact', 'customer_id', $this->id, $this->contacts);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('CustomerSkill', 'customer_id', $id);
        //parent::deleteComposite('Contact', 'customer_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }
}
?>