/* globals Chart:false, feather:false */

(function () {
  'use strict'

  feather.replace()

  // Graphs
  var ctx = document.getElementById('myChart')
  // eslint-disable-next-line no-unused-vars
  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [
        '2012 S1',
        '2012 S2',
        '2013 S1',
        '2013 S2',
        '2014 S1',
        '2014 S2',
        '2015 S1',
        '2015 S2',
        '2016 S1'
      ],
      datasets: [{
        data: [
          39,
          45,
          83,
          3,
          89,
          92,
          34,
          45,
          83
        ],
        
        lineTension: 0,
        backgroundColor: 'transparent',
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff'
      }]
      ,
      
    },
    options: {
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: false
          }
        }]
      },
      legend: {
        display: false
      }
    }
  })
}())
