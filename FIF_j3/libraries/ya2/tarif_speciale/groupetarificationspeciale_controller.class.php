<?php
require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . "DBAL/data/groupetarificationspeciale_data.class.php");

/**
 * Définition du controller de la classe GroupeTarificationSpeciale
 *
 * @author DIGITARTS - 28 Jan 2016
 *        
 *        
 */
class GroupeTarificationSpecialeController extends GroupeTarificationSpecialeData {

	/**
	 * Mets à jour GroupeTarificationSpeciale
	 * 
	 * @param
	 *        	GroupeTarificationSpecialeEntity GroupeTarificationSpeciale
	 * @return number
	 *
	 */
	function UpdateGroupeTarificationSpeciale(GroupeTarificationSpecialeEntity $groupetarificationspeciale) {
		return $this->UpdateGroupeTarificationSpecialeData ( $groupetarificationspeciale );
	}

	/**
	 * Ajoute une GroupeTarificationSpeciale
	 * 
	 * @param
	 *        	GroupeTarificationSpecialeEntity GroupeTarificationSpeciale
	 * @return GroupeTarificationSpecialeEntity|NULL
	 *
	 */
	function AddGroupeTarificationSpeciale(GroupeTarificationSpecialeEntity $groupetarificationspeciale) {
		$result = $this->AddGroupeTarificationSpecialeData ( $groupetarificationspeciale );
		if ($result != - 1) {
			$groupetarificationspeciale->SetId ( $result );
			return $groupetarificationspeciale;
		}
		return null;
	}

	/**
	 * Récupère une GroupeTarificationSpeciale via ID
	 *
	 * @param integer $groupetarificationspecialeId        	
	 * @return GroupeTarificationSpecialeEntity
	 *
	 */
	function GetGroupeTarificationSpecialeById($groupetarificationspecialeId) {
		return $this->GetGroupeTarificationSpecialeClassFromArray ( $this->GetGroupeTarificationSpecialeByIdData ( $groupetarificationspecialeId ) );
	}

	/**
	 * Recupère la liste complète des GroupeTarificationSpeciale
	 *
	 * @return multitype:GroupeTarificationSpecialeEntity
	 *
	 */
	function GetAllGroupeTarificationSpeciale() {
		$result = $this->GetAllGroupeTarificationSpecialeData ();
		$groupetarificationspecialeList = array ();
		if ($result == null)
			return $groupetarificationspecialeList;
		foreach ( $result as $entry ) {
			$groupetarificationspecialeList [] = $this->GetGroupeTarificationSpecialeClassFromArray ( $entry );
		}
		return $groupetarificationspecialeList;
	}

	/**
	 * Cree un objet de type groupetarificationspeciale à partir du post
	 *
	 * @param array $post        	
	 * @return GroupeTarificationSpecialeEntity
	 *
	 */
	function CaptureGroupeTarificationSpecialeFromPost($post) {
		$groupetarificationspeciale = new GroupeTarificationSpecialeEntity ();
		if (isset ( $post ['gts_id'] )) {
			$groupetarificationspeciale->Setid ( $post ['gts_id'] );
		}
		if (isset ( $post ['gts_nom'] )) {
			$groupetarificationspeciale->Setnom ( $post ['gts_nom'] );
		}
		if (isset ( $post ['gts_date_creation'] )) {
			$groupetarificationspeciale->Setdatecreation ( $post ['gts_date_creation'] );
		}
		if (isset ( $post ['gts_date_derniere_modification'] )) {
			$groupetarificationspeciale->Setdatedernieremodification ( $post ['gts_date_derniere_modification'] );
		}
		if (isset ( $post ['gts_tarif_hc'] )) {
			$groupetarificationspeciale->Settarifhc ( $post ['gts_tarif_hc'] );
		}
		if (isset ( $post ['gts_description'] )) {
			$groupetarificationspeciale->Setdescription ( $post ['gts_description'] );
		}
		return $groupetarificationspeciale;
	}

	/**
	 * Cree une class à partir du tableau
	 *
	 * @param array $entry        	
	 * @return GroupeTarificationSpecialeEntity
	 *
	 */
	protected function GetGroupeTarificationSpecialeClassFromArray($entry) {
		if ($entry == null)
			return null;
		$groupetarificationspeciale = new GroupeTarificationSpecialeEntity ( array (
				'id' => $entry ['gts_id'],
				'nom' => $entry ['gts_nom'],
				'datecreation' => $entry ['gts_date_creation'],
				'datedernieremodification' => $entry ['gts_date_derniere_modification'],
				'tarifhc' => $entry ['gts_tarif_hc'],
				'description' => $entry ['gts_description'] 
		) );
		return $groupetarificationspeciale;
	}

	/**
	 * Retourne l'entité GroupeTarificationSpeciale sous forme de tableau
	 *
	 * @param GroupeTarificationSpecialeEntity $groupetarificationspeciale        	
	 * @return array
	 * @deprecated
	 *
	 */
	public function GetGroupeTarificationSpecialeAsArray(GroupeTarificationSpecialeEntity $groupetarificationspeciale) {
		$groupetarificationspecialeArr = array ();
		$groupetarificationspecialeArr ['gts_id'] = $groupetarificationspeciale->Getid ();
		$groupetarificationspecialeArr ['gts_nom'] = $groupetarificationspeciale->Getnom ();
		$groupetarificationspecialeArr ['gts_date_creation'] = $groupetarificationspeciale->Getdatecreation ();
		$groupetarificationspecialeArr ['gts_date_derniere_modification'] = $groupetarificationspeciale->Getdatedernieremodification ();
		$groupetarificationspecialeArr ['gts_tarif_hc'] = $groupetarificationspeciale->Gettarifhc ();
		$groupetarificationspecialeArr ['gts_description'] = $groupetarificationspeciale->Getdescription ();
		return $groupetarificationspecialeArr;
	}
}