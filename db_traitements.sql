-- @file db_traitements.sql
-- @brief Effectue les traitements sur la base de donnée qui sera utilisée par l'API grâce au fichier .csv disponible en open data

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- AVANT IMPORT

-- On créé les tables si elles n'existent pas déjà
CREATE TABLE IF NOT EXISTS `MAJOPE`(
  `IDENTITE_OPERATEUR` text COLLATE utf8_general_ci NOT NULL,
  `CODE_OPERATEUR` varchar(5) COLLATE utf8_general_ci NOT NULL,
  `SIRET_ACTEUR` varchar(14) COLLATE utf8_general_ci NOT NULL,
  `RCS_ACTEUR` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `ADRESSE_COMPLETE_ACTEUR` text COLLATE utf8_general_ci NOT NULL,
  `BESOIN_RES_NUM` varchar(1) COLLATE utf8_general_ci NOT NULL,
  `DATE_DECLARATION_OPERATEUR` varchar(20) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `CONCATENATION`(
  `Identite` text COLLATE utf8_general_ci NOT NULL,
  `Code` varchar(5) COLLATE utf8_general_ci NOT NULL,
  `SIRET` varchar(14) COLLATE utf8_general_ci NOT NULL,
  `RCS` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `Adresse` text COLLATE utf8_general_ci NOT NULL,
  `Besoin_Numerotation` varchar(1) COLLATE utf8_general_ci NOT NULL,
  `Date_Declaration` varchar(20) COLLATE utf8_general_ci NOT NULL,
  `Date_Declaration_MEF` varchar(8) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `CONCATENATION_DATE`(
  `Nb_Declarations` int COLLATE utf8_general_ci NOT NULL,
  `Date_Declaration` DATE COLLATE utf8_general_ci NOT NULL,
  `Date_Declaration_MEF` varchar(8) COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `CALENDAR`( -- Pour gérer le cas des dates où aucun opérateur n'a été déclaré
  `datefield` DATE
);

-- On supprime le contenu des tables
DELETE FROM `MAJOPE` WHERE 1;
DELETE FROM `CONCATENATION` WHERE 1;
DELETE FROM `CONCATENATION_DATE` WHERE 1;
DELETE FROM `CALENDAR` WHERE 1;

-- On créé la procédure pour remplir la table CALENDAR
DROP PROCEDURE IF EXISTS fill_calendar;

CREATE PROCEDURE fill_calendar(start_date DATE, end_date DATE)
BEGIN
DECLARE crt_date DATE;
SET crt_date=start_date;
WHILE crt_date < end_date DO
INSERT INTO calendar VALUES(crt_date);
SET crt_date = ADDDATE(crt_date, INTERVAL 1 DAY);
END WHILE;
END;

-- IMPORT

-- On importe le fichier dans la table
-- Attention à modifier si nécessaire le fichier de confifguration de mysql (my.ini ou my.cnf) pour ajouter la ligne secure_file_priv="" à la fin en cas d'erreur !
-- Attention : l'insertion de données nécessite un chemin complet et non un chemin relatif, modifier le chemin avant d'exécuter le script !

-- On insère le fichier MAJOPE dans la table MAJOPE
LOAD DATA LOCAL INFILE "/CHEMIN/ABSOLU/VERS/LE/DOSSIER/temp/MAJOPE_utf8.csv"
INTO TABLE MAJOPE
CHARACTER SET UTF8
FIELDS
TERMINATED BY ';'
ENCLOSED BY '"'
LINES
TERMINATED BY '\r\n'
IGNORE 1 LINES;

-- On remplit la table CALENDAR
CALL fill_calendar('1987-01-01', DATE(NOW()));

-- APRES IMPORT

-- On insère la table MAJNUM dans la table CONCATENATION
INSERT INTO CONCATENATION (Identite, Code, SIRET, RCS, Adresse, Besoin_Numerotation, Date_Declaration, Date_Declaration_MEF)
SELECT IDENTITE_OPERATEUR AS Identite, CODE_OPERATEUR AS Code, SIRET_ACTEUR AS SIRET, RCS_ACTEUR AS RCS, ADRESSE_COMPLETE_ACTEUR AS Adresse, BESOIN_RES_NUM AS Besoin_Numerotation, left(DATE_DECLARATION_OPERATEUR,10) AS Date_Declaration,  right(left(DATE_DECLARATION_OPERATEUR,10),4)*10000+left(right(left(DATE_DECLARATION_OPERATEUR,10),7),2)*100+left(left(DATE_DECLARATION_OPERATEUR,10),2) AS Date_Declaration_MEF
FROM MAJOPE;

-- On crée une table spécifique pour récupérer le nombre de déclarations par jour
INSERT INTO CONCATENATION_DATE(Nb_Declarations, Date_Declaration, Date_Declaration_MEF)
SELECT IFNULL(COUNT(CODE),0) AS Nb_Declarations, CALENDAR.datefield AS Date_Declaration, YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield) AS Date_Declaration_MEF
FROM CONCATENATION
RIGHT JOIN CALENDAR ON( CONCATENATION.Date_Declaration_MEF =( YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield) ) )
GROUP BY YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield);
