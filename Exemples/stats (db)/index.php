<?php
/**
* @file index.php
* @brief Exemple d'utilisation possible des données de l'API
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
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/0.6.6/chartjs-plugin-zoom.min.js"></script>
	<script type="text/javascript" src="./graph.js"></script>
</head>

<body>
	<!-- Le formulaire qui sera utilisé -->
	<form name="form" method="post" action="index.php" id="form">
		<input type="radio" id="nbDeclarationsAn" name="choix" value="nbDeclarationsAn" class="radioSelect" required>Nombre de déclarations annuelles
		<input type="radio" id="nbDeclarationsCumuleesAn" name="choix" value="nbDeclarationsCumuleesAn" class="radioSelect" required>Nombre de déclarations annuelles cumulées
		<input type="radio" id="nbDeclarationsMois" name="choix" value="nbDeclarationsMois" class="radioSelect" required>Nombre de déclarations mensuelles
		<input type="radio" id="nbDeclarationsCumuleesMois" name="choix" value="nbDeclarationsCumuleesMois" class="radioSelect" required> Nombre de déclarations mensuelles cumulées
		<input type="radio" id="nbDeclarations12Mois" name="choix" value="nbDeclarations12Mois" class="radioSelect" required>Nombre de déclarations des 12 derniers mois
		<br />
		<input type="submit" name="submit"></input>
	</form>

	<?php
	if(isset($_POST["choix"]) && $_POST["choix"] != ""){
		$radioValue = $_POST["choix"]; // On récupère la valeur du bouton radio
		if($radioValue == "nbDeclarationsAn"){
			echo "
			<script>
			showGraphAnnee();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasAnnee'></canvas>
			</div>";
		}else if($radioValue == "nbDeclarationsCumuleesAn"){
			echo "
			<script>
			showGraphCumulAn();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasCumulAn'></canvas>
			</div>";
		}else if($radioValue == "nbDeclarationsMois"){
			echo "
			<script>
			showGraphMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasMois'></canvas>
			</div>";
		}else if($radioValue == "nbDeclarationsCumuleesMois"){
			echo "
			<script>
			showGraphCumulMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasCumulMois'></canvas>
			</div>";
		}
		else if($radioValue == "nbDeclarations12Mois"){
			echo "
			<script>
			showGraphDerniersMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasDerniersMois'></canvas>
			</div>";
		}
	}
	?>

	<div id= "tableau"></div>

</body>
</html>
