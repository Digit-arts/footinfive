<?php
require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . "DBAL/data/clienttarificationspeciale_data.class.php");

/**
 * Définition du controller de la classe ClientTarificationSpeciale
 *
 * @author DIGITARTS - 30 Jan 2016
 *        
 *        
 */
class ClientTarificationSpecialeController extends ClientTarificationSpecialeData {

	/**
	 * Mets à jour ClientTarificationSpeciale
	 * 
	 * @param
	 *        	ClientTarificationSpecialeEntity ClientTarificationSpeciale
	 * @return number
	 *
	 */
	function UpdateClientTarificationSpeciale(ClientTarificationSpecialeEntity $clienttarificationspeciale) {
		return $this->UpdateClientTarificationSpecialeData ( $clienttarificationspeciale );
	}

	/**
	 * Ajoute une ClientTarificationSpeciale
	 * 
	 * @param
	 *        	ClientTarificationSpecialeEntity ClientTarificationSpeciale
	 * @return ClientTarificationSpecialeEntity|NULL
	 *
	 */
	function AddClientTarificationSpeciale(ClientTarificationSpecialeEntity $clienttarificationspeciale) {
		$result = $this->AddClientTarificationSpecialeData ( $clienttarificationspeciale );
		if ($result != - 1) {
			$clienttarificationspeciale->SetId ( $result );
			return $clienttarificationspeciale;
		}
		return null;
	}

	/**
	 * Récupère une ClientTarificationSpeciale via ID
	 *
	 * @param integer $clienttarificationspecialeId        	
	 * @return ClientTarificationSpecialeEntity
	 *
	 */
	function GetClientTarificationSpecialeById($clienttarificationspecialeId) {
		return $this->GetClientTarificationSpecialeClassFromArray ( $this->GetClientTarificationSpecialeByIdData ( $clienttarificationspecialeId ) );
	}
	
	/**
	 * Récupère une ClientTarificationSpeciale via ID client
	 *
	 * @param integer $clientId
	 * @return ClientTarificationSpecialeEntity
	 *
	 */
	function GetClientTarificationSpecialeByClientId($clientId) {
		return $this->GetClientTarificationSpecialeClassFromArray ( $this->GetClientTarificationSpecialeByClientIdData ( $clientId ) );
	}

	/**
	 * Recupère la liste complète des ClientTarificationSpeciale
	 *
	 * @return multitype:ClientTarificationSpecialeEntity
	 *
	 */
	function GetAllClientTarificationSpeciale() {
		$result = $this->GetAllClientTarificationSpecialeData ();
		$clienttarificationspecialeList = array ();
		if ($result == null)
			return $clienttarificationspecialeList;
		foreach ( $result as $entry ) {
			$clienttarificationspecialeList [] = $this->GetClientTarificationSpecialeClassFromArray ( $entry );
		}
		return $clienttarificationspecialeList;
	}

	/**
	 * Cree un objet de type clienttarificationspeciale à partir du post
	 *
	 * @param array $post        	
	 * @return ClientTarificationSpecialeEntity
	 *
	 */
	function CaptureClientTarificationSpecialeFromPost($post) {
		$clienttarificationspeciale = new ClientTarificationSpecialeEntity ();
		if (isset ( $post ['cts_id'] )) {
			$clienttarificationspeciale->Setid ( $post ['cts_id'] );
		}
		if (isset ( $post ['cts_client_id'] )) {
			$clienttarificationspeciale->Setclientid ( $post ['cts_client_id'] );
		}
		if (isset ( $post ['cts_gts_id'] )) {
			$clienttarificationspeciale->Setgtsid ( $post ['cts_gts_id'] );
		}
		if (isset ( $post ['cts_date_modification'] )) {
			$clienttarificationspeciale->Setdatemodification ( $post ['cts_date_modification'] );
		}
		return $clienttarificationspeciale;
	}

	/**
	 * Cree une class à partir du tableau
	 *
	 * @param array $entry        	
	 * @return ClientTarificationSpecialeEntity
	 *
	 */
	protected function GetClientTarificationSpecialeClassFromArray($entry) {
		if ($entry == null)
			return null;
		$clienttarificationspeciale = new ClientTarificationSpecialeEntity ( array (
				'id' => $entry ['cts_id'],
				'clientid' => $entry ['cts_client_id'],
				'gtsid' => $entry ['cts_gts_id'],
				'datemodification' => $entry ['cts_date_modification'] 
		) );
		return $clienttarificationspeciale;
	}

	/**
	 * Retourne l'entité ClientTarificationSpeciale sous forme de tableau
	 *
	 * @param ClientTarificationSpecialeEntity $clienttarificationspeciale        	
	 * @return array
	 * @deprecated
	 *
	 */
	public function GetClientTarificationSpecialeAsArray(ClientTarificationSpecialeEntity $clienttarificationspeciale) {
		$clienttarificationspecialeArr = array ();
		$clienttarificationspecialeArr ['cts_id'] = $clienttarificationspeciale->Getid ();
		$clienttarificationspecialeArr ['cts_client_id'] = $clienttarificationspeciale->Getclientid ();
		$clienttarificationspecialeArr ['cts_gts_id'] = $clienttarificationspeciale->Getgtsid ();
		$clienttarificationspecialeArr ['cts_date_modification'] = $clienttarificationspeciale->Getdatemodification ();
		return $clienttarificationspecialeArr;
	}
}