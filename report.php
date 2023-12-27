<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

   <!-- Favicons -->
   <link rel="apple-touch-icon" href="img/Logo pemadam kebakaran.png" sizes="180x180">
  <link rel="icon" href="img/Logo pemadam kebakaran.png" sizes="32x32" type="image/png">
  <link rel="icon" href="img/Logo pemadam kebakaran.png" sizes="16x16" type="image/png">
  <link rel="manifest" href="manifest.json">
  <link rel="mask-icon" href="safari-pinned-tab.svg" color="#712cf9">
  <link rel="icon" href="img/Logo pemadam kebakaran.png">
  <meta name="theme-color" content="#712cf9">

</head>

<body>
  <div class=" d-flex justify-content-center align-items-center">
    <canvas id="chart-report" class="h-25"></canvas>
  </div>
  <script>
    const ctx = document.getElementById("chart-report")

    let dataLoad = []
    let dataLoad24 = []

    function filterDataByMonth(datas, targetMonth) {
      const filteredData = datas.filter((data) => {
        const date = new Date(data.waktu);
        return date.getMonth() + 1 === targetMonth; // Mendapatkan bulan (mulai dari 0)
      }); 
      return filteredData;
    }

    function filterDataByYear(datas, targetYear) {
      const filteredData = datas.filter((data) => {
        const date = new Date(data.waktu);
        return date.getFullYear() === targetYear; // Mendapatkan bulan (mulai dari 0)
      }); 
      return filteredData;
    }
    const getData = async () => {
      try {
        const response = await fetch("/PendeteksiLokasiKebakaran/Visualstudio/index.php").then(res => res.json()).then(json => json).catch(error => error)

        const data2023 = filterDataByYear(response.data, 2023) 
        const data2024 = filterDataByYear(response.data, 2024) 
        
        for (let index = 0; index < 12; index++) {
          const month = filterDataByMonth(data2023, index + 1) 
          const length = month.length
          dataLoad.push(length)
        }
        for (let index = 0; index < 12; index++) {
          const month = filterDataByMonth(data2024, index + 1) 
          const length = month.length
          dataLoad24.push(length)
        }
        console.log(dataLoad)
        return response.data
      } catch (error) {
        console.log(error)
      }
    }

    getData();

    const labels = ["","JAN", "FEB", "MAR", "APR", "JUN", "JUL", "AGU", "SEP", "OKT", "NOV", "DES"];
    const data = {
      labels: labels,
      datasets: [{
          label: '2023',
          data: dataLoad,
          borderColor: "#00000",
          backgroundColor: "#000000",
        },
        {
          label: '2024',
          data: dataLoad24,
          borderColor: "blue",
          backgroundColor: "blue",
        }
      ]
    };

    const config = {
      type: 'line',
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Report Kebakaran Jakarta'
          }
        }
      },
    };
    new Chart(ctx, config)
    </script>

</body>

</html>