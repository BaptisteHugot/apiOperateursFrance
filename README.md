# apiOperateursFrance
Cette API permet d'effectuer des recherches sur les déclarations d'opérateurs effectuées par l'Arcep. Les données sont tirées du fichier des déclarations des opérateurs mis à disposition par l'[Arcep](https://www.data.gouv.fr/fr/datasets/operateurs-de-communications-electroniques/) en open data.

## Utilisation
L'API permet d'effectuer une recherche selon les critères suivants pour les déclarations d'opérateurs :
* A un opérateur spécifique, à l'aide du mot-clé OPERATEUR suivi d'un code alphanumérique sur 4 ou 5 caractères
* Avant une date donnée, à l'aide du mot-clé DATEINF suivi d'un nombre sur 8 caractères au format DDMMAAAA
* Après une date donnée, à l'aide du mot-clé DATESUP suivi d'un nombre sur 8 caractères au format DDMMAAAA
* Entre deux dates données, à l'aide des mots-clés DATEENTREINF suivi d'un nombre sur 8 caractères au format DDMMAAAA et DATEENTRESUP suivi d'un nombre sur 8 caractères au format DDMMAAAA
L'API retourne null dans les cas où aucun élément n'est trouvé ou si les données en entrée ne respectent pas le format attendu ou sont incorrectes (par exemple, si une date entrée n'existe pas).

Le projet contient également un fichier permettant d'automatiser le processus de récupération des fichiers et d'inclusion dans une base de données pour maintenir à jour les données de l'API.

## Ecrit avec
* [PHP](https://secure.php.net/) - Le langage de programmation utilisé pour l'API, le traitement des données et la page d'exemple
* [SQL](https://www.iso.org/standard/63555.html) - Le langage de programmation utilisé pour stocker les éléments dans une base de données
* [HTML](https://www.w3.org/html/) - Le langage de programmation utilisé pour afficher la page d'exemple
* [CSS](https://www.w3.org/Style/CSS/) - Le langage de programmation utilisé pour gérer les styles de la page d'exemple
* [Javascript](https://www.ecma-international.org/publications/standards/Ecma-262.htm) - Le langage de programmation utilisé pour gérer une partie des styles de la page d'exemple

## Bibliothèques utilisées
* [jQuery](https://jquery.com/) - La bibliothèque utilisée pour gérer une partie des styles de la page d'exemple
* [Chart.js](https://www.chartjs.org/) - La bibliothèque utilisée pour afficher des graphiques

## Exemples
Le projet contient 3 exemples d'utilisation distincts :
* Un permettant de rechercher différentes informations via l'API grâce à cURL
* Un permettant de rechercher différentes informations via la base de données
* Un affichant différents graphiques grâce à des informations récupérées via la base de données, dont vous trouverez quelques graphiques au format .png ci-dessous :
<img src="https://www.baptistehugot.cf/github/images/nbDeclarationsMensuellesCumul.png" width="45%"></img> <img src="https://www.baptistehugot.cf/github/images/nbDeclarationsAnnuellesCumul.png" width="45%"></img> 

## Versions
[SemVer](http://semver.org/) est utilisé pour la gestion de versions. Pour connaître les versions disponibles, veuillez vous référer aux [étiquettes disponibles dans ce dépôt](https://github.com/BaptisteHugot/apiOperateursFrance/releases/).

## Auteurs
* **Baptiste Hugot** - *Travail initial* - [BaptisteHugot](https://github.com/BaptisteHugot)

## Licence
Ce projet est disponible sous licence logiciel MIT. Veuillez lire le fichier [LICENSE](LICENSE) pour plus de détails.

## Règles de conduite
Pour connaître l'ensemble des règles de conduite à respecter sur ce dépôt, veuillez lire le fichier [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## Contribution au projet
Si vous souhaitez contribuer au projet, que ce soit en corrigeant des bogues ou en proposant de nouvelles fonctionnalités, veuillez lire le fichier [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.