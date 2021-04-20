// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';


testAjax(function(output){
  // alert(output);

  var chartLabels = new Array();
  var chartValues = new Array();
  const words = output.split(',');

  for (i = 0; i < words.length; i++) {
    const outString = words[i].replace(/[`~!@#$%^&*()|+\-=?;'",.<>\{\}\[\]\\\/]/gi, '');
    const newWords = outString.split(':');
    const newWordsTemp = newWords[0].split('_');

    var labelText = newWordsTemp[0];
    var labelValue = parseInt(newWords[1]);



    chartLabels.push(labelText);
    chartValues.push(labelValue);


  }
  // console.log(chartData);
  // alert(JSON.stringify(labelValue));


  // alert(words[0]);

  // document.getElementById("demo").innerHTML = res;




  // Pie Chart Example
  var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      // labels: ["Direct", "Referral", "Social"],
      labels: chartLabels,
      datasets: [{
        // data: [55, 30, 15],
        data: chartValues,
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
});


function testAjax(handleData) {
  // alert('return sent');
  $.ajax({
      type: "POST",
      url: "db_connect_charts",
      data: 'chartType=pie&dataType=count&dataNeeds=Repairs_Replacements',
      success: function(data) {
      // alert(JSON.stringify(data));
        // alert(data); // apple
        handleData(data); 
      }
  });
}


