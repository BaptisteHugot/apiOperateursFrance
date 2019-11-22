/**
* @file graph.js
* @brief Définit les graphiques qui seront affichés sur la page d'index
*/

/**
* Fonction définissant le graphe représentant la répartition annuelle des déclarations qui sera affiché
*/
function showGraphAnnee()
{
  {
    $.post("data.php",
    {action: 'annee'},
    function (data)
    {
      console.log(data);
      var annee = [];
      var nbDec = [];

      for (var i in data) {
        annee.push(data[i].annee);
        nbDec.push(data[i].nbDec);
      }

      var chartdata = {
        labels: annee,
        datasets: [
          {
            label: 'Nombre de déclarations',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbDec
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].annee+"</td>";
        html+="<td>"+data[i].nbDec+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasAnnee");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition annuelle des déclarations"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition annuelle cumulée des déclarations qui sera affiché
*/
function showGraphCumulAn()
{
  {
    $.post("data.php",
    {action: 'cumulAn'},
    function (data)
    {
      console.log(data);
      var annee = [];
      var nbDec = [];

      for (var i in data) {
        annee.push(data[i].annee);
        nbDec.push(data[i].nbDec);
      }

      var chartdata = {
        labels: annee,
        datasets: [
          {
            label: 'Nombre de déclarations annuelles cumulées',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbDec
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].annee+"</td>";
        html+="<td>"+data[i].nbDec+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasCumulAn");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition annuelle cumulée des déclarations"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle des déclarations qui sera affiché
*/
function showGraphMois()
{
  {
    $.post("data.php",
    {action: 'mois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbDec = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbDec.push(data[i].nbDec);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de déclarations',
            backgroundColor: [
              '#90335d',
              '#ffcc99',
              '#ccff99',
              '#9999ff',
              '#fd9bca',
              '#2d3657',
              '#cb7993',
              '#b6cae9',
              '#0a7599',
              '#101010',
              '#d7897e',
              '#fdfbfa',
            ],
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbDec
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbDec+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasMois");

      var pieChart = new Chart(graphTarget, {
        type: 'pie',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition mensuelle des déclarations"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle cumulée des déclarations qui sera affiché
*/
function showGraphCumulMois()
{
  {
    $.post("data.php",
    {action: 'cumulMois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbDec = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbDec.push(data[i].nbDec);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de déclarations mensuelles cumulées',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbDec
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbDec+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasCumulMois");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Répartition mensuelle cumulée des déclarations"
          }
        }
      });
    });
  }
}

/**
* Fonction définissant le graphe représentant la répartition mensuelle des 12 derniers mois des déclarations qui sera affiché
*/
function showGraphDerniersMois()
{
  {
    $.post("data.php",
    {action: 'derniersMois'},
    function (data)
    {
      console.log(data);
      var mois = [];
      var nbDec = [];

      for (var i in data) {
        mois.push(data[i].mois);
        nbDec.push(data[i].nbDec);
      }

      var chartdata = {
        labels: mois,
        datasets: [
          {
            label: 'Nombre de déclarations',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: nbDec
          }
        ]
      };

      var html = "<table border='1|1'>";
      for (var i = 0; i < data.length; i++) {
        html+="<tr>";
        html+="<td>"+data[i].mois+"</td>";
        html+="<td>"+data[i].nbDec+"</td>";
        html+="</tr>";

      }
      html+="</table>";
      document.getElementById("tableau").innerHTML = html;

      var graphTarget = $("#graphCanvasDerniersMois");

      var barGraph = new Chart(graphTarget, {
        type: 'bar',
        data: chartdata,
        responsive: true,
        options: {
          title: {
            display: true,
            text: "Déclarations lors des 12 derniers mois"
          }
        }
      });
    });
  }
}
