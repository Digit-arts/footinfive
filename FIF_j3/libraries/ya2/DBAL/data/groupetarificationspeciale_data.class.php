<?php
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "data.class.php");
require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . "entities/groupetarificationspeciale_entity.class.php");

/**
 * Couche SQL : GroupeTarificationSpeciale
 *
 * @author DIGITARTS - 27 Jan 2016
 *        
 *        
 */
class GroupeTarificationSpecialeData extends Data {

	protected function UpdateGroupeTarificationSpecialeData(GroupeTarificationSpecialeEntity $groupetarificationspeciale) {
		$query = "UPDATE groupe_tarification_speciale SET
				gts_nom = :nom,
				gts_date_creation = :datecreation,
				gts_date_derniere_modification = :datedernieremodification,
				gts_tarif_hc = :tarifhc,
				gts_description = :description
				WHERE gts_id = :id;";
		return self::UpdateMulti ( $query, $groupetarificationspeciale->ToArray () );
	}

	protected function AddGroupeTarificationSpecialeData(GroupeTarificationSpecialeEntity $groupetarificationspeciale) {
		$query = "INSERT INTO groupe_tarification_speciale (`gts_nom`,`gts_date_creation`,`gts_date_derniere_modification`,`gts_tarif_hc`,`gts_description`)
		VALUES (:nom,:datecreation,:datedernieremodification,:tarifhc,:description);";
		return self::InsertSingleLine ( $query, $groupetarificationspeciale->ToArrayNoId () );
	}

	protected function GetGroupeTarificationSpecialeByIdData($groupetarificationspecialeId) {
		$query = "SELECT * FROM groupe_tarification_speciale WHERE gts_id = :groupetarificationspecialeId ;";
		
		$paramId = array();
		$paramId[0] = ':groupetarificationspecialeId';
		$paramId[1] = $groupetarificationspecialeId;
		$paramId[2] = 'PDO::PARAM_INT';
		$params[] = $paramId;
		
		return self::QuerySingleLine ( $query, $params );
	}

	protected function GetAllGroupeTarificationSpecialeData() {
		$query = "SELECT * FROM groupe_tarification_speciale";
		return self::QueryMultipleLine ( $query );
	}
}
?>