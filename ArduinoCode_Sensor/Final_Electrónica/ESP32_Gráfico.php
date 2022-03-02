<?php
$servername = "localhost";
// REPLACE with your Database name
$dbname = "id17202099_bdsensor";
// REPLACE with Database user
$username = "id17202099_christye";
// REPLACE with Database user password
$password = "QPsP3SmQaTV_+g)m";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, value1, value2, value3, reading_time FROM SensorData order by reading_time desc limit 40";
$result = $conn->query($sql) or die($conn->error);
while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}
$readings_time = array_column($sensor_data, 'reading_time');
$i = 0;
foreach ($readings_time as $reading){
    $readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading + 7 hours")); //convert timezone to +7
    $i += 1;
}
$value1 = json_encode(array_reverse(array_column($sensor_data, 'value1')), JSON_NUMERIC_CHECK);
$value2 = json_encode(array_reverse(array_column($sensor_data, 'value2')), JSON_NUMERIC_CHECK);
$value3 = json_encode(array_reverse(array_column($sensor_data, 'value3')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);
/*echo $value1;
echo $value2;
echo $value3;
echo $reading_time;*/
$result->free();
$conn->close();
?>




<!DOCTYPE html>
<html>
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="https://code.highcharts.com/highcharts.js"></script>
 <script src="https://code.highcharts.com/modules/exporting.js"></script>
 <script src="https://code.highcharts.com/modules/export-data.js"></script>
 <script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <style>
    body {
      min-width: 200px;
      max-width: 1280px;
      height: 500px;
      margin: 0 auto;
      color: blue;
    }
    h2 {
      font-family: Arial;
      font-size: 2.5rem;
      text-align: center;
      color: red;
      background-image: url("https://mdn.mozillademos.org/files/11991/startransparent.gif");
    }
  </style>
  <body>
    <h2>ESP32 Gráfico_Electrónica</h2>
    <div id="chart-temperature" class="container"></div>
    <div id="chart-approximate-altitude" class="container"></div>
    <div id="chart-pressure" class="container"></div>
    <script>
       var value1 = <?php echo $value1; ?>;
       var value2 = <?php echo $value2; ?>;
       var value3 = <?php echo $value3; ?>;
       var reading_time = <?php echo $reading_time; ?>;

       // GRAFICO TEMPERATURA
      $(function () {
        $(document).ready(function () {
        var ultimovalue1;
       $.ajax({
               url: "consulta.php",
               type: 'get',
               success: function(DatosRecuperados) {
               $.each(DatosRecuperados, function(i,o){
                   if (o.x) {DatosRecuperados[i].x = parseFloat(o.x);}
                 });
                 setvalue1(DatosRecuperados[(DatosRecuperados.length)-1].x);

         $('#container').highcharts({
             chart: { renderTo : 'chart-temperature' },
             title: { text: 'Temperatura BMP180' },
             series: [{
                showInLegend: false,
                data: DatosRecuperados
             }],
             plotOptions: {
                line: { animation: false,
                    dataLabels: { enabled: true }
                },
                series: { color: '#059e8a' }
             },
             xAxis: {
                type: 'datetime',
                categories: reading_time
             },
             yAxis: {
                title: { text: 'Temperatura (Celsius)' }
                //title: { text: 'Temperature (Fahrenheit)' }
             },
             credits: { enabled: false }
         });
       }});
       });
       setInterval(function () {
             $.get( "datos.php?Consultar=1", function( UltimosDatos ) {
                 var varlocalx=parseFloat(UltimosDatos[0].x);

              if((getvalue1()!=varlocalx)){
                 series.addPoint([varlocalx], true, true);
                 setvalue1(varlocalx);
             }
        });}, 1000);




         function getvalue1(){return ultimovalue1;}
         function setvalue1(){ultimovalue1=x;}

});

//GRAFICO PRESION

var chartH = new Highcharts.Chart({
  chart:{ renderTo:'chart-pressure' },
  title: { text: 'Presión BMP180' },
  series: [{
    showInLegend: false,
    data: value2
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    }
  },
  xAxis: {
    type: 'datetime',
    //dateTimeLabelFormats: { second: '%H:%M:%S' },
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Presión (Pa)' }
  },
  credits: { enabled: false }
});




//GRAFICO ALTITUD

var chartP = new Highcharts.Chart({
  chart:{ renderTo:'chart-approximate-altitude' },
  title: { text: 'Altitud aproximada BMP180' },
  series: [{
    showInLegend: false,
    data: value3
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#18009c' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Altitud Aproximada (m)' }
  },
  credits: { enabled: false }
});
    </script>
</body>
</html>
