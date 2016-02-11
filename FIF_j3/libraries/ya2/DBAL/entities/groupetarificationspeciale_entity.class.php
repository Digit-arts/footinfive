<?php
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "record.class.php");

/**
 * GroupeTarificationSpeciale entity
 *
 * @author DIGITARTS - 27 Jan 2016
 *        
 *        
 */
class GroupeTarificationSpecialeEntity extends Record {
	
	// FIELD : gts_id TYPE : tinyint(4) NULL : NO KEY : PRI DEFAULT : EXTRA : auto_increment
	protected $id;
	const INVALID_ID = 0;

	/**
	 * Retourne la valeur du champs gts_id
	 */
	public function Getid() {
		return $this->id;
	}

	/**
	 * Retourne la type du champs gts_id
	 */
	public function GetidPDOType() {
		return "PDO::PARAM_INT";
	}

	/**
	 * Definit la valeur du champs gts_id
	 * 
	 * @param
	 *        	tinyint id
	 *        	
	 */
	public function Setid($id) {
		$this->id = $id;
	}
	
	// FIELD : gts_nom TYPE : varchar(50) NULL : NO KEY : DEFAULT : EXTRA :
	protected $nom = '';
	const INVALID_NOM = 1;

	/**
	 * Retourne la valeur du champs gts_nom
	 */
	public function Getnom() {
		return $this->nom;
	}

	/**
	 * Retourne la type du champs gts_nom
	 */
	public function GetnomPDOType() {
		return "PDO::PARAM_STR";
	}

	/**
	 * Definit la valeur du champs gts_nom
	 * 
	 * @param
	 *        	varchar nom
	 *        	
	 */
	public function Setnom($nom) {
		if (empty ( $nom ) || ! is_string ( $nom )) {
			$this->erreurs [] = self::INVALID_NOM;
		} else {
			$this->nom = $nom;
		}
	}
	
	// FIELD : gts_date_creation TYPE : datetime NULL : NO KEY : DEFAULT : EXTRA :
	protected $date_creation = '';
	const INVALID_DATE_CREATION = 2;

	/**
	 * Retourne la valeur du champs gts_date_creation
	 */
	public function Getdatecreation() {
		return $this->date_creation;
	}

	/**
	 * Retourne la type du champs gts_date_creation
	 */
	public function GetdatecreationPDOType() {
		return "PDO::PARAM_STR";
	}

	/**
	 * Definit la valeur du champs gts_date_creation
	 * 
	 * @param
	 *        	date date_creation
	 *        	
	 */
	public function Setdatecreation($date_creation) {
		$this->date_creation = $date_creation;
	}
	
	// FIELD : gts_date_derniere_modification TYPE : datetime NULL : NO KEY : DEFAULT : EXTRA :
	protected $date_derniere_modification = '';
	const INVALID_DATE_DERNIERE_MODIFICATION = 3;

	/**
	 * Retourne la valeur du champs gts_date_derniere_modification
	 */
	public function Getdatedernieremodification() {
		return $this->date_derniere_modification;
	}

	/**
	 * Retourne la type du champs gts_date_derniere_modification
	 */
	public function GetdatedernieremodificationPDOType() {
		return "PDO::PARAM_STR";
	}

	/**
	 * Definit la valeur du champs gts_date_derniere_modification
	 * 
	 * @param
	 *        	date date_derniere_modification
	 *        	
	 */
	public function Setdatedernieremodification($date_derniere_modification) {
		$this->date_derniere_modification = $date_derniere_modification;
	}
	
	// FIELD : gts_tarif_hc TYPE : tinyint(4) NULL : NO KEY : DEFAULT : EXTRA :
	protected $tarif_hc = 0;
	const INVALID_TARIF_HC = 4;

	/**
	 * Retourne la valeur du champs gts_tarif_hc
	 */
	public function Gettarifhc() {
		return $this->tarif_hc;
	}

	/**
	 * Retourne la type du champs gts_tarif_hc
	 */
	public function GettarifhcPDOType() {
		return "PDO::PARAM_INT";
	}

	/**
	 * Definit la valeur du champs gts_tarif_hc
	 * 
	 * @param
	 *        	tinyint tarif_hc
	 *        	
	 */
	public function Settarifhc($tarif_hc) {
		$this->tarif_hc = $tarif_hc;
	}
	
	// FIELD : gts_description TYPE : text NULL : YES KEY : DEFAULT : EXTRA :
	protected $description;
	const INVALID_DESCRIPTION = 5;

	/**
	 * Retourne la valeur du champs gts_description
	 */
	public function Getdescription() {
		return $this->description;
	}

	/**
	 * Retourne la type du champs gts_description
	 */
	public function GetdescriptionPDOType() {
		return "PDO::PARAM_STR";
	}

	/**
	 * Definit la valeur du champs gts_description
	 * 
	 * @param
	 *        	text description
	 *        	
	 */
	public function Setdescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Liste des proprietes de l'entites
	 * 
	 * @var properties
	 *
	 */
	protected $properties;

	public function __construct(array $donnees = array()) {
		$this->properties = array (
				'id',
				'nom',
				'datecreation',
				'datedernieremodification',
				'tarifhc',
				'description' 
		);
		parent::__construct ( $donnees );
	}

	/**
	 * Renvoie un tableau associatif base sur le contenu de l'objet
	 * 
	 * @see Record::ToArray()
	 *
	 */
	public function ToArray() {
		return parent::ToArrayBase ( $this->properties );
	}

	public function ToArrayNoId() {
		$propertiesNoId = array ();
		foreach ( $this->properties as $prop ) {
			if (strtolower ( $prop ) != 'id') {
				$propertiesNoId [] = $prop;
			}
		}
		return parent::ToArrayBase ( $propertiesNoId );
	}
}
?>