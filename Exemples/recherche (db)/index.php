<?php

/**
* @file index.php
* @brief Exemple d'utilisation possible de la base de données avec un fichier mis en forme
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Exemple d'utilisation de l'API opérateurs</title>
	<link rel="StyleSheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" type="text/css" />

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
	<script src="./style.js"></script>
</head>

<body>
	<!-- Le formulaire qui sera utilisé -->
	<form name="form" method="post" action="index.php" id="form">
		<input type="radio" id="operateur" name="choix" value="operateur" class="radioSelect" required>Opérateur
		<input type="radio" id="dateInf" name="choix" value="dateInf" class="radioSelect" required>Avant le :
		<input type="radio" id="dateSup" name="choix" value="dateSup" class="radioSelect" required>Après le :
		<input type="radio" id="dateEntre" name="choix" value="dateEntre" class="radioSelect" required>Entre :
		<br />

		<!-- On modifie le champ d'entrée de texte selon le bouton radio choisi -->
		<input type="text" class="specificField" id="operateur" name="operateur" placeholder="Opérateur : " minlength="3" maxlength="5" />
		<input type="text" class="specificField" id="dateInf" name="dateInf" placeholder="Avant le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="dateSup" name="dateSup" placeholder="Après le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="dateEntre" name="dateEntreInf" placeholder="Après le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="dateEntre" name="dateEntreSup" placeholder="Et avant le : " minlength="8" maxlength="8" />
		<br />

		<input type="submit" name="submit"></input>
	</form>

</body>
</html>

<?php
if(isset($_POST["choix"]) && $_POST["choix"] != ""){
	$radioValue = $_POST["choix"]; // On récupère la valeur du bouton radio
	if($radioValue == "operateur"){
		/* Recherche des numéros attribués à un opérateur donné (via son code Arcep) */
		if(isset($_POST["operateur"]) && $_POST["operateur"] != ""){
			$operateur = $_POST["operateur"];

			$operateur = htmlspecialchars($operateur, ENT_QUOTES, 'UTF-8'); // Pour éviter une injection XSS

			$regEx = "#^[A-Za-z0-9]{4,5}$#"; // Expression régulière du code opérateur

			if(preg_match($regEx, $operateur)){ // Si le code opérateur entré correspond à l'expression régulière
				include('./../../db.php'); // On se connecte à la base de données

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

					if($jsonData[0] == null){ // Dans le cas où l'API retourne null, afin d'éviter d'afficher un tableau vide
						echo "Votre recherche n'a retourné aucune donnée";
					}else{
						$i = 0;
						// On affiche un tableau avec l'ensemble des éléments correspondants à la requête demandée
						echo "<table>";
						echo "<tr><td>Identité</td><td>Code</td><td>SIRET</td><td>RCS</td><td>Adresse</td><td>Besoin_Numérotation</td><td>Date_Déclaration</td></tr>";
						foreach($jsonData as $item){ // Pour chaque élément, on ajoute une nouvelle ligne au tableau
							echo "<tr>";
							echo "<td>" . $jsonData[$i]['Identite'] . "</td>";
							echo "<td>" . $jsonData[$i]['Code'] . "</td>";
							echo "<td>" . $jsonData[$i]['SIRET'] . "</td>";
							echo "<td>" . $jsonData[$i]['RCS'] . "</td>";
							echo "<td>" . $jsonData[$i]['Adresse'] . "</td>";
							echo "<td>" . $jsonData[$i]['Besoin_Numerotation'] . "</td>";
							echo "<td>" . $jsonData[$i]['Date_Declaration'] . "</td>";
							echo "</tr>";
							$i++;
						}
						echo "</table>";
					}
				}else echo "Votre recherche n'a retourné aucune donnée";
			}
		}
	}else if($radioValue == "dateInf"){
		/* Recherche des attributions avant une date donnée */
		if(isset($_POST["dateInf"]) && $_POST["dateInf"] != ""){
			$date = $_POST["dateInf"];

			$date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');

			$regEx = "#^[0-9]{8}$#";

			if(preg_match($regEx, $date) && validateDate($date)){
				$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2);

				include('./../../db.php');

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

					if($jsonData[0] == null){
						echo "Votre recherche n'a retourné aucune donnée";
					}else{
						$i = 0;
						echo "<table>";
						echo "<tr><td>Identité</td><td>Code</td><td>SIRET</td><td>RCS</td><td>Adresse</td><td>Besoin_Numérotation</td><td>Date_Déclaration</td></tr>";
						foreach($jsonData as $item){
							echo "<tr>";
							echo "<td>" . $jsonData[$i]['Identite'] . "</td>";
							echo "<td>" . $jsonData[$i]['Code'] . "</td>";
							echo "<td>" . $jsonData[$i]['SIRET'] . "</td>";
							echo "<td>" . $jsonData[$i]['RCS'] . "</td>";
							echo "<td>" . $jsonData[$i]['Adresse'] . "</td>";
							echo "<td>" . $jsonData[$i]['Besoin_Numerotation'] . "</td>";
							echo "<td>" . $jsonData[$i]['Date_Declaration'] . "</td>";
							echo "</tr>";
							$i++;
						}
						echo "</table>";
					}
				}else echo "Votre recherche n'a retourné aucune donnée";
			}
		}
	}else if($radioValue == "dateSup"){
		/* Recherche des attributions après une date donnée */
		if(isset($_POST["dateSup"]) && $_POST["dateSup"] != ""){
			$date = $_POST["dateSup"];

			$date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');

			$regEx = "#^[0-9]{8}$#";

			if(preg_match($regEx, $date) && validateDate($date)){
				$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2);

				include('./../../db.php');

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

					if($jsonData[0] == null){
						echo "Votre recherche n'a retourné aucune donnée";
					}else{
						$i = 0;
						echo "<table>";
						echo "<tr><td>Identité</td><td>Code</td><td>SIRET</td><td>RCS</td><td>Adresse</td><td>Besoin_Numérotation</td><td>Date_Déclaration</td></tr>";
						foreach($jsonData as $item){
							echo "<tr>";
							echo "<td>" . $jsonData[$i]['Identite'] . "</td>";
							echo "<td>" . $jsonData[$i]['Code'] . "</td>";
							echo "<td>" . $jsonData[$i]['SIRET'] . "</td>";
							echo "<td>" . $jsonData[$i]['RCS'] . "</td>";
							echo "<td>" . $jsonData[$i]['Adresse'] . "</td>";
							echo "<td>" . $jsonData[$i]['Besoin_Numerotation'] . "</td>";
							echo "<td>" . $jsonData[$i]['Date_Declaration'] . "</td>";
							echo "</tr>";
							$i++;
						}
						echo "</table>";
					}
				}else echo "Votre recherche n'a retourné aucune donnée";
			}
		}
	}else if($radioValue == "dateEntre"){
		/* Recherche des attributions entre deux dates données */
		if(isset($_POST["dateEntreInf"]) && $_POST["dateEntreInf"] != "" && isset($_POST["dateEntreSup"]) && $_POST["dateEntreSup"] != ""){
			$dateInf = $_POST["dateEntreInf"];
			$dateSup = $_POST["dateEntreSup"];

			$dateInf = htmlspecialchars($dateInf, ENT_QUOTES, 'UTF-8');
			$dateSup = htmlspecialchars($dateSup, ENT_QUOTES, 'UTF-8');

			$regEx = "#^[0-9]{8}$#";

			if(preg_match($regEx, $dateInf) && preg_match($regEx, $dateSup) && validateDate($dateInf) && validateDate($dateSup)){
				$date_inf = substr($dateInf, 4, 4) * 10000 + substr($dateInf, 2, 2) * 100 + substr($dateInf, 0, 2);
				$date_sup = substr($dateSup, 4, 4) * 10000 + substr($dateSup, 2, 2) * 100 + substr($dateSup, 0, 2);

				include('./../../db.php');

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

					if($jsonData[0] == null){
						echo "Votre recherche n'a retourné aucune donnée";
					}else{
						$i = 0;
						echo "<table>";
						echo "<tr><td>Identité</td><td>Code</td><td>SIRET</td><td>RCS</td><td>Adresse</td><td>Besoin_Numérotation</td><td>Date_Déclaration</td></tr>";
						foreach($jsonData as $item){
							echo "<tr>";
							echo "<td>" . $jsonData[$i]['Identite'] . "</td>";
							echo "<td>" . $jsonData[$i]['Code'] . "</td>";
							echo "<td>" . $jsonData[$i]['SIRET'] . "</td>";
							echo "<td>" . $jsonData[$i]['RCS'] . "</td>";
							echo "<td>" . $jsonData[$i]['Adresse'] . "</td>";
							echo "<td>" . $jsonData[$i]['Besoin_Numerotation'] . "</td>";
							echo "<td>" . $jsonData[$i]['Date_Declaration'] . "</td>";
							echo "</tr>";
							$i++;
						}
						echo "</table>";
					}
				}else echo "Votre recherche n'a retourné aucune donnée";
			}
		}
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
?>
