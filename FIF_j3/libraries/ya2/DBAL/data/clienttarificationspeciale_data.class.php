<?php
require_once (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . "data.class.php");
require_once (dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . "entities/clienttarificationspeciale_entity.class.php");

/**
 * Couche SQL : ClientTarificationSpeciale
 *
 * @author DIGITARTS - 30 Jan 2016
 *        
 *        
 */
class ClientTarificationSpecialeData extends Data {

	protected function UpdateClientTarificationSpecialeData(ClientTarificationSpecialeEntity $clienttarificationspeciale) {
		$query = "UPDATE client_tarification_speciale SET
		cts_client_id = :clientid,
		cts_gts_id = :gtsid,
		cts_date_modification = :datemodification
		WHERE cts_id = :id;";
		return self::UpdateMulti ( $query, $clienttarificationspeciale->ToArray () );
	}

	protected function AddClientTarificationSpecialeData(ClientTarificationSpecialeEntity $clienttarificationspeciale) {
		$query = "INSERT INTO client_tarification_speciale (`cts_client_id`,`cts_gts_id`,`cts_date_modification`)
		VALUES (:clientid,:gtsid,:datemodification);";
		return self::InsertSingleLine ( $query, $clienttarificationspeciale->ToArrayNoId () );
	}

	protected function GetClientTarificationSpecialeByIdData($clienttarificationspecialeId) {
		$query = "SELECT * FROM client_tarification_speciale WHERE cts_id = :clienttarificationspecialeId ;";
		$paramId = array();
		$paramId[0] = ':clienttarificationspecialeId';
		$paramId[1] = $clienttarificationspecialeId;
		$paramId[2] = 'PDO::PARAM_INT';
		$params[] = $paramId;
		return self::QuerySingleLine ( $query, $params );
	}
	
	protected function GetClientTarificationSpecialeByClientIdData($clientId) {
		$query = "SELECT * FROM client_tarification_speciale WHERE cts_client_id = :clientId AND cts_gts_id!=-1;";
		$paramId = array();
		$paramId[0] = ':clientId';
		$paramId[1] = $clientId;
		$paramId[2] = 'PDO::PARAM_INT';
		$params[] = $paramId;
		return self::QuerySingleLine ( $query, $params );
	}
	

	protected function GetAllClientTarificationSpecialeData() {
		$query = "SELECT * FROM client_tarification_speciale";
		return self::QueryMultipleLine ( $query );
	}
}
?>