// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function getData(url, callback,labels,nombre){
    var req = new XMLHttpRequest();
    req.open("GET", url);
    req.addEventListener("load", function () {
        if (req.status >= 200 && req.status < 400) {
            callback(req.responseText,labels,nombre);
        } else {
            console.error(req.status + " " + req.statusText + " " + url);
        }
    });
    req.addEventListener("error", function () {
        console.error("Erreur réseau avec l'URL " + url);
    });
    req.send(null);
    //var data = [12,20,30,40,50,62,68,70,89,23,11,12];
    //return data;
}
function resultat(info,labels,nombre){
  result = JSON.parse(info);
  result['status']["libelle"].forEach(function(item){
    labels.push(item);
  });
  result['status']["nombre"].forEach(function(items){
     if (Number.isInteger(items)) {
        nombre.push(items)
      }
  });
  console.log(nombre)
  console.log(labels)
}

// Pie Chart Example
var labels=[];
var data=[];
getData("http://127.0.0.1:8000/recep/charts/test/check",resultat,labels,data);
var data = [12,20,30,40];
console.log(typeof data)
console.log(typeof labels)
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: labels,
    datasets: [{
      data: data,
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});
