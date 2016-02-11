<?php

/**
 * Classe abstraite Record représentant un enregistrement
 * @author DIGIARTS 20 Janvier 2016
 */
abstract class Record implements ArrayAccess{
    
    protected $erreurs = array();
    
    protected $id;
    
    public function __construct(array $donnees = array()) {
        if(!empty($donnees)) {
            $this->hydrate($donnees);            
        }
    }
    
    public function isNew() {
        return empty($this->id);
    }
    
    public function erreurs() {
        return $this->erreurs;
    }
    
    public function addErreur($erreur) {
        if(!empty($erreur)) {
            $this->erreurs[] = $erreur;
        }
    }
    
    public function id() {
        return $this->id;
    }
	
	public function getAllProperties($utf8encode = false)
	{
		$tab_res = array();
		foreach ($this->properties as $prop)
		{
			$methode = 'Get' . $prop;
			$tab_res[$prop] = $this->$methode();
			if ($utf8encode)
				$tab_res[$prop] = utf8_encode($tab_res[$prop]);
		}
		
		return $tab_res;
	}
	
   
    public function hydrate(array $donnees) {
        foreach($donnees as $attribut => $valeur) {
            $methode = 'set'.str_replace(' ','',ucwords(str_replace('_',' ',$attribut)));
            if(is_callable(array($this,$methode))) {
                $this->$methode($valeur);
            }
        }
    }
    
    public function offsetGet($var)
    {
    	$var = 'Get'.$var;
        if (isset($this->$var) && is_callable(array($this, $var)))
        {
            return $this->$var();
        }
    }
    
    public function offsetSet($var, $value)
    {
        $method = 'set'.ucfirst($var);
        
        if (isset($this->$var) && is_callable(array($this, $method)))
        {
            $this->$method($value);
        }
    }
    
    public function offsetExists($var)
    {
        return isset($this->$var) && is_callable(array($this, $var));
    }
    
    public function offsetUnset($var)
    {
    	throw new RuntimeException("Impossible de supprimer une quelconque valeur ($var)");
    }
    
    public function ToArrayBase($properties)
    {
    	$recordAsArray = array ();
    	foreach($properties as $prop) {
    		$recordLineAsArray = array();
    		$method = 'Get'.$prop;
    		$methodType = 'Get'.$prop.'PDOType';
    		$recordLineAsArray[0] = ":".$prop;
    		$recordLineAsArray[1] = $this->$method();   
    		$recordLineAsArray[2] = $this->$methodType();
    		$recordAsArray[] = $recordLineAsArray;
    	}
    	
    	return $recordAsArray;
    }
}