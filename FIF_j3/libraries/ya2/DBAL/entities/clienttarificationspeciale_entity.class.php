<?php
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "record.class.php");

/**
 * ClientTarificationSpeciale entity
 *
 * @author DIGITARTS - 30 Jan 2016
 *        
 *        
 */
class ClientTarificationSpecialeEntity extends Record {
	
	// FIELD : cts_id TYPE : int(11) NULL : NO KEY : PRI DEFAULT : EXTRA : auto_increment
	protected $id;
	const INVALID_ID = 0;

	/**
	 * Retourne la valeur du champs cts_id
	 */
	public function Getid() {
		return $this->id;
	}

	/**
	 * Retourne la type du champs cts_id
	 */
	public function GetidPDOType() {
		return "PDO::PARAM_INT";
	}

	/**
	 * Definit la valeur du champs cts_id
	 * 
	 * @param
	 *        	int id
	 *        	
	 */
	public function Setid($id) {
		if (! is_numeric ( $id )) {
			$this->erreurs [] = self::INVALID_ID;
		} else {
			$this->id = $id;
		}
	}
	
	// FIELD : cts_client_id TYPE : int(11) NULL : NO KEY : DEFAULT : EXTRA :
	protected $client_id = - 1;
	const INVALID_CLIENT_ID = 1;

	/**
	 * Retourne la valeur du champs cts_client_id
	 */
	public function Getclientid() {
		return $this->client_id;
	}

	/**
	 * Retourne la type du champs cts_client_id
	 */
	public function GetclientidPDOType() {
		return "PDO::PARAM_INT";
	}

	/**
	 * Definit la valeur du champs cts_client_id
	 * 
	 * @param
	 *        	int client_id
	 *        	
	 */
	public function Setclientid($client_id) {
		if (! is_numeric ( $client_id )) {
			$this->erreurs [] = self::INVALID_CLIENT_ID;
		} else {
			$this->client_id = $client_id;
		}
	}
	
	// FIELD : cts_gts_id TYPE : tinyint(4) NULL : NO KEY : DEFAULT : EXTRA :
	protected $gts_id = - 1;
	const INVALID_GTS_ID = 2;

	/**
	 * Retourne la valeur du champs cts_gts_id
	 */
	public function Getgtsid() {
		return $this->gts_id;
	}

	/**
	 * Retourne la type du champs cts_gts_id
	 */
	public function GetgtsidPDOType() {
		return "PDO::PARAM_INT";
	}

	/**
	 * Definit la valeur du champs cts_gts_id
	 * 
	 * @param
	 *        	tinyint gts_id
	 *        	
	 */
	public function Setgtsid($gts_id) {
		$this->gts_id = $gts_id;
	}
	
	// FIELD : cts_date_modification TYPE : date NULL : NO KEY : DEFAULT : EXTRA :
	protected $date_modification = '';
	const INVALID_DATE_MODIFICATION = 3;

	/**
	 * Retourne la valeur du champs cts_date_modification
	 */
	public function Getdatemodification() {
		return $this->date_modification;
	}

	/**
	 * Retourne la type du champs cts_date_modification
	 */
	public function GetdatemodificationPDOType() {
		return "PDO::PARAM_STR";
	}

	/**
	 * Definit la valeur du champs cts_date_modification
	 * 
	 * @param
	 *        	date date_modification
	 *        	
	 */
	public function Setdatemodification($date_modification) {
		$this->date_modification = $date_modification;
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
				'clientid',
				'gtsid',
				'datemodification' 
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