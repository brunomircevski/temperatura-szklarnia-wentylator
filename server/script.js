const ctx = document.getElementById('chart');

const time = [];
const greenhouseTemp = [];
const indoorTemp = [];
const outdoorTemp = [];
const fan = [];

records.forEach(r => {
    time.push(r[0]);
    greenhouseTemp.push(r[1]);
    indoorTemp.push(r[2]);
    outdoorTemp.push(r[3]);
    if(r[4]==1) fan.push(45);
    else fan.push(NaN);
});

const data = {
    labels: time,
    datasets: [
        {
            label: 'Szklarnia',
            data: greenhouseTemp,
            borderColor: 'green',
            backgroundColor: 'green',
            fill: false,
            cubicInterpolationMode: 'monotone',
            tension: 0.4,
            pointRadius: 1
        },
        {
            label: 'Dom',
            data: indoorTemp,
            borderColor: 'orange',
            backgroundColor: 'orange',
            fill: false,
            cubicInterpolationMode: 'monotone',
            tension: 0.4,
            pointRadius: 1
        },
        {
            label: 'Zewnętrzna',
            data: outdoorTemp,
            borderColor: 'blue',
            backgroundColor: 'blue',
            fill: false,
            cubicInterpolationMode: 'monotone',
            tension: 0.4,
            pointRadius: 1
        },  
        {
            label: 'Wentylator',
            data: fan,
            borderColor: 'gray',
            fill: true,
            pointRadius: 0,
            hidden: true
        }
    ]
};

const config = {
    type: 'line',
    data: data,
    optiive: true,
      pluginsons: {
      respons: {
        title: {
          display: true,
          text: 'Wykres temperatury z dnia'
        },
      },
      interaction: {
        intersect: false,
      },
      scales: {
        x: {
          display: true,
          title: {
            display: true
          }
        },
        y: {
          display: true,
          title: {
            display: true,
            text: 'Temperatura'
          },
        }
      }
    },
  };

const myChart = new Chart(ctx, config);
