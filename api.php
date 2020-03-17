<?php
/**
* @file api.php
* @brief Génère l'API pour rechercher un opérateur déclaré, les déclarations effectuées avant et/ou après une date donnée
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

header("Content-Type:application/json");

/**
* Effectue des traitements sur la chaîne de caractères entrée par l'utilisateur
* @param $string La chaîne de caractères entrée par l'utilisateur
* @return $string La chaîne de caractères traitée
*/
function cleanEntry(string $string) : string{
	$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8'); // Pour éviter une injection XSS
	$string = strtoupper($string); // On met en majuscule les données entrées

	return $string;
}

/**
* Retourne les données concernant un opérateur donné
* @param $operateur Le code Arcep de l'opérateur
*/
function jsonOperateur(string $operateur){
	$regEx = "#^[A-Za-z0-9]{4,5}$#"; // Expression régulière d'un code Arcep
	$operateur = cleanEntry($operateur);

	if(preg_match($regEx, $operateur)){ // Si l'opérateur entré correspond à l'expression régulière
		include('db.php'); // On se connecte à la base de données

		$operateur = $connexion->real_escape_string($operateur); // Pour éviter une injection SQL
		$stmt = $connexion->prepare("SELECT Identite, Code, SIRET, RCS, Adresse, Besoin_Numerotation, Date_Declaration FROM CONCATENATION WHERE Code = ?"); // Requête SQL à exécuter
		$stmt->bind_param("s", $operateur); // On vérifie que le type de variable est correct
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){ // On arrête le programme si l'exécution de la requête a rencontré un problème
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){ // Si au moins un élément est trouvé
			while($array = mysqli_fetch_assoc($result)){ // On stocke chaque ligne de la base de données dans une ligne d'un tableau PHP
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); // On affiche le résultat au format JSON
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
		}else { // On retourne null si aucun élément n'est trouvé
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
		}
	}else{ // On retourne null si le format entré ne correspond pas à l'expression régulière
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/**
* Retourne la liste des déclarations après une date donnée
* @param $date Date à partir de laquelle les données doivent être retournées
*/
function jsonDateSup(string $date){
	$date = cleanEntry($date);

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $date) && validateDate($date)){
		$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2); // On met en forme la date entrée pour pouvoir requêter plus facilement

		include('db.php');

		$date_mef = $connexion->real_escape_string($date_mef);
		$stmt = $connexion->prepare("SELECT Identite, Code, SIRET, RCS, Adresse, Besoin_Numerotation, Date_Declaration FROM CONCATENATION WHERE (CAST(Date_Declaration_MEF AS UNSIGNED) >= ?) ORDER BY Date_Declaration_MEF");
		$stmt->bind_param("s", $date_mef);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/**
* Retourne la liste des déclarations avant une date donnée
* @param $date Date avant laquelle les données doivent être retournées
*/
function jsonDateInf(string $date){
	$date = cleanEntry($date);

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $date) && validateDate($date)){
		$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2);

		include('db.php');

		$date_mef = $connexion->real_escape_string($date_mef);
		$stmt = $connexion->prepare("SELECT Identite, Code, SIRET, RCS, Adresse, Besoin_Numerotation, Date_Declaration FROM CONCATENATION WHERE (CAST(Date_Declaration_MEF AS UNSIGNED) <= ?) ORDER BY Date_Declaration_MEF");
		$stmt->bind_param("s", $date_mef);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/**
* Retourne la liste des déclarations entre deux dates données
* @param $dateInf Date à partir de laquelle les données doivent être retournées
* @param $dateSup Date avant laquelle les données doivent être retournées
*/
function jsonDateEntre(string $dateInf, string $dateSup){
	$dateInf = cleanEntry($dateInf);
	$dateSup = cleanEntry($dateSup);

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $dateInf) && preg_match($regEx, $dateSup) && validateDate($dateInf) && validateDate($dateSup)){
		$date_inf = substr($dateInf, 4, 4) * 10000 + substr($dateInf, 2, 2) * 100 + substr($dateInf, 0, 2);
		$date_sup = substr($dateSup, 4, 4) * 10000 + substr($dateSup, 2, 2) * 100 + substr($dateSup, 0, 2);

		include('db.php');

		$date_inf = $connexion->real_escape_string($date_inf);
		$date_sup = $connexion->real_escape_string($date_sup);
		$stmt = $connexion->prepare("SELECT Identite, Code, SIRET, RCS, Adresse, Besoin_Numerotation, Date_Declaration FROM CONCATENATION WHERE (CAST(Date_Declaration_MEF AS UNSIGNED) >= ?) AND (CAST(Date_Declaration_MEF AS UNSIGNED) <= ?) ORDER BY Date_Declaration_MEF");
		$stmt->bind_param("ss", $date_inf, $date_sup);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/**
* Vérification que la date entrée est correcte (année bissextile comprise) et est inférieure ou égale à la date du jour
* @param $date La date d'entrée à vérifier
* @param $format Le format de la date (JJMMAAAA)
* @return bool Vrai si la date existe et est inférieure ou égale à la date du jour, Faux sinon
*/
function validateDate($date, $format="dmY"){
	$d = DateTime::createFromFormat($format, $date);
	$now = (new DateTime('now'));

	return $d && ($d->format($format) === $date) && ($d->format($format) <= $now);
}

/**
* Fonction qui sert à traiter les différents cas d'appel de l'API
*/
function appelAPI(){
	if(isset($_GET['OPERATEUR']) && $_GET['OPERATEUR']!=""){
		$operateur = $_GET['OPERATEUR'];
		jsonOperateur($operateur);
	}else if(isset($_GET['DATESUP']) && $_GET['DATESUP'] != ""){
		$date = $_GET['DATESUP'];
		jsonDateSup($date);
	}else if(isset($_GET['DATEINF']) && $_GET['DATEINF'] != ""){
		$date = $_GET['DATEINF'];
		jsonDateInf($date);
	}else if(isset($_GET['DATEENTREINF']) && $_GET['DATEENTREINF'] != "" && isset($_GET['DATEENTRESUP']) && $_GET['DATEENTRESUP'] != ""){
		$dateInf = $_GET['DATEENTREINF'];
		$dateSup = $_GET['DATEENTRESUP'];
		jsonDateEntre($dateInf, $dateSup);
	}
}

appelAPI();
?>
