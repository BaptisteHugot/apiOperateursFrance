<?php
/**
* @file db_traitements.php
* @brief Fichier effectuant les traitements sur les fichiers disponibles en open data au format xls pour les inclure dans une table MySQL commune
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

/**
* Fonction principale du programme
*/
function dbTraitements(){
	// On définit l'ensemble des dépendances
	include ('db.php');

	// On définit l'ensemble des variables
	// L'URL de téléchargement du fichier en open data
	$urlOperateurs = "https://www.data.gouv.fr/fr/datasets/r/f1c8eb9a-22e7-4f67-a402-53aabe9c9f7a";

	$tempSaveFolder = "./temp/"; // Dossier où seront mis les fichiers temporaires
	$fileSQL = "./db_traitements.sql"; // Le chemin relatif où se situe le script sql à exécuter

	// On crée le dossier /temp/ si celui-ci n'existe pas déjà
	if(!file_exists($tempSaveFolder)){
		mkdir($tempSaveFolder, 0777, true);
	}

	// On télécharge le fichier disponible en open data
	downloadFile($urlOperateurs, $tempSaveFolder . "MAJOPE.csv", "MAJOPE");

	// On convertir le fichier téléchargé au format UTF-8
	convertUTF8($tempSaveFolder, 'MAJOPE.csv', 'MAJOPE_utf8.csv');

	// On insère les fichiers au format .csv dans la base de données et on effectue les traitements adéquats
	insertionBDD($connexion, $fileSQL);

	// On supprime le fichier
	deleteFile($tempSaveFolder . "MAJOPE.csv", "MAJOPE");
}

/**
* Téléchargement d'un fichier via son URL et enregistrement à un endroit précisé
* @param $fileUrl L'URL du fichier à télécharger
* @param $saveTo L'endrot où le fichier sera sauvegardé
* @param $name Le nom du fichier sauvegardé
*/
function downloadFile($fileUrl, $saveTo, $name){
	$start = microtime(true); // Début du chronomètre

	$fp = fopen($saveTo, 'w+'); // On créé un fichier en écriture

	if($fp == false){ // Si le fichier ne peut pas être ouvert
		throw new Exception("Ne peut pas ouvrir : " . $saveTo . nl2br("\n"));
	}

	$ch = curl_init($fileUrl); // On créé un gestionnaire cURL
	curl_setopt($ch, CURLOPT_FILE, $fp); // On passe le fichier au gestionnaire cURL
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // On stoppe si le fichier n'est pas téléchargé après 20 secondes
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); // On force toutes les éventuelles redirections mises en place
	curl_exec($ch); // On exécute la requête

	if(curl_errno($ch)){ // Si un message d'erreur cURL existe
		throw new Exception(curl_error($ch));
	}

	$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // On récupèle le code de statut HTTP
	curl_close($ch); // On ferme le gestionnaire cURL
	fclose($fp); // On ferme le fichier

	$end = microtime(true); // Fin du chronomètre

	if($statusCode == 200){
		echo "Fichier : " . $name . " téléchargé en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
	} else {
		echo "Statut : " . $statusCode . " " . nl2br("\n");
	}
}

/**
* Convertit le fichier d'entrée au format UTF-8
* @param $tempSaveFolder Le fichier où sont stockés les fichiers
* @param $input_file Le fichier d'entrée
* @param $output_file Le fichier de sortie
*/
function convertUTF8($tempSaveFolder, $input_file, $output_file){
	$start = microtime(true); // Début du chronomètre

	$file_data = file_get_contents($tempSaveFolder . $input_file);
	$utf8_file_data = utf8_encode($file_data);
	$new_file_name = $tempSaveFolder . $output_file;
	file_put_contents($new_file_name , $utf8_file_data );

	$end = microtime(true); // Fin du chronomètre

	echo "Conversion du fichier des opérateurs en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
}

/**
* Insertion d'un fichier .sql dans la base de données
* @param $connexion La connexion à la base de données
* @param $myfile Le fichier au format .sql qui doit être inséré
*/
function insertionBDD($connexion, $myfile){
	$start = microtime(true);

	$sqlSource = file_get_contents($myfile);
	mysqli_multi_query($connexion, $sqlSource) or die("Impossible d'exécuter le fichier SQL" . nl2br("\n")); // On exécute le fichier au format .sql

	// On attend que l'ensemble des requêtes SQL du script se soient exécutées
	while(mysqli_next_result($connexion)){

	}

	if(mysqli_error($connexion)){
		die(mysqli_error($connexion));
	}

	$end = microtime(true);

	echo "Insertion des fichiers dans la base de données réussie en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
}

/**
* Suppression d'un fichier donné
* @param $myfile Le fichier à supprimer
* @param $name Le nom du fichier à supprimer
*/
function deleteFile($myfile, $name){
	$start = microtime(true);

	unlink($myfile) or die("Impossible de supprimer le fichier " . $myfile . nl2br("\n")); // On supprime le fichier

	$end = microtime(true);
	echo "Fichier " . $myfile . " supprimé en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
}

dbTraitements();

?>
