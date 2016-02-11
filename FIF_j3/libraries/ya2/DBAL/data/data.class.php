<?php

/**
 * Classe data de base
 * @author DIGITARTS - 20 janvier 2016
 *
 */
class Data {
	private $dao;

	/**
	 * Etabli la connexion par défaut
	 *
	 * @return Connexion MySQL
	 */
	protected function GetSQLConnection() {
		// captures de la configuration joomla
		$db = JFactory::getDBO ();
		$config = JFactory::getConfig ();
		$host = $config->get ( 'hostNoPort' );
		$port = $config->get ( 'dbPort' );
		$db = $config->get ( 'db' );
		$user = $config->get ( 'user' );
		$password = $config->get ( 'password' );
		
		$this->dao = new PDO ( 'mysql:host=' . $host.';port=' . $port . ';dbname=' . $db, $user, $password );
	}

	/**
	 * Envoie une requete à résultat unique
	 *
	 * @param
	 *        	string Requete
	 * @param
	 *        	Parametres (nom, valeur, type)
	 *        	
	 * @return array enregistrement
	 */
	protected function QuerySingleLine($query, $params = []) {
		
		// Initialiser la connexion
		self::GetSQLConnection ();
		
		$sth = $this->dao->prepare ( $query );
		
		foreach ( $params as $param ) {
			if ($param [2] == 'PDO::PARAM_STR') {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_STR );
			} else {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_INT );
			}
		}
		
		$result = $sth->execute ();
		
		if (! $result) {
			print_r ( $result );
			return - 1;
		}
		
		if ($sth->rowCount () == 0)
			return null;
		return $sth->fetch ( PDO::FETCH_ASSOC );
	}

	/**
	 * Compte le nombre d'enregistrements retournés pour une requete
	 *
	 * @param
	 *        	string Requete
	 * @param
	 *        	Parametres (nom, valeur, type)
	 * @return integer count
	 */
	protected function QueryCount($query, $params = []) {
		
		// Initialiser la connexion
		self::GetSQLConnection ();
		
		$sth = $this->dao->prepare ( $query );
		foreach ( $params as $param ) {
			if ($param [2] == 'PDO::PARAM_STR') {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_STR );
			} else {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_INT );
			}
		}
		$result = $sth->execute ();
		
		if (! $result) {
			print_r ( $result );
			return - 1;
		}
		
		return $sth->rowCount ();
	}

	/**
	 * Envoie une requete à résultats multiples
	 *
	 * @param
	 *        	string Requete
	 * @param
	 *        	Parametres (nom, valeur, type)
	 *        	
	 * @return List d'enregistrements
	 */
	protected function QueryMultipleLine($query, $params = []) {
		
		// Initialiser la connexion
		self::GetSQLConnection ();
		
		$sth = $this->dao->prepare ( $query );
		
		foreach ( $params as $param ) {
			if ($param [2] == 'PDO::PARAM_STR') {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_STR );
			} else {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_INT );
			}
		}
		
		$result = $sth->execute ();
		
		if (! $result) {
			print_r ( $result );
			return - 1;
		}
		
		if ($sth->rowCount () == 0)
			return array ();
		return $sth->fetchAll ( PDO::FETCH_ASSOC );
	}

	/**
	 * Insere un enregistrement unique
	 *
	 * @param
	 *        	string Requete
	 * @param
	 *        	Parametres (keyValue pair)
	 * @return integer ID de l'enregistrement créé
	 */
	protected function InsertSingleLine($query, $params = []) {
		
		// Initialiser la connexion
		self::GetSQLConnection ();
		
		$sth = $this->dao->prepare ( $query );
		foreach ( $params as $param ) {
			if ($param [2] == 'PDO::PARAM_STR') {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_STR );
			} else {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_INT );
			}
		}
		
		$result = $sth->execute ();
		
		if ($result) {
			return $this->dao->lastInsertId ();
		} else {
			return - 1;
		}
	}

	/**
	 * Mets à jour une table
	 *
	 * @param string $query        	
	 * @param
	 *        	Parametres (keyValue pair)
	 */
	protected function UpdateMulti($query, $params = []) {
		// Initialiser la connexion
		self::GetSQLConnection ();
		
		$sth = $this->dao->prepare ( $query );
		foreach ( $params as $param ) {
			if ($param [2] == 'PDO::PARAM_STR') {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_STR );
			} else {
				$paramEntry = $sth->bindParam ( $param [0], $param [1], PDO::PARAM_INT );
			}
		}
		
		$result = $sth->execute ();
		
		if ($result) {
			return $sth->rowCount ();
		} else {
			return - 1;
		}
	}

	/**
	 * Supprime les entrées d'une table
	 *
	 * @param string $query        	
	 * @param
	 *        	Parametres (keyValue pair)
	 */
	protected function DeleteMulti($query, $params = []) {
		return self::UpdateMulti ( $query, $params );
	}
}

?>