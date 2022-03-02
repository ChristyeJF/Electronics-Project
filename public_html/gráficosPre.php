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
  <script src="jquery-2.1.4.js"></script>
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
       
       
       
       // GRAFICO PRESION
      $(function () {  
        $(document).ready(function () {
        var ultimovalue3;
       
        
       $.ajax({
               url: "consultaPre.php",
               type: 'get',
               success: function(DatosRecuperadosp) {
               $.each(DatosRecuperadosp, function(i,o){
                   if (o.x) {DatosRecuperadosp[i].x = parseFloat(o.x);}
                   
                   
                 });
                 setvalue3(DatosRecuperadosp[(DatosRecuperadosp.length)-1].x);
                 
                 
                 $('#chart-temperature').highcharts({
                    chart:{
                            type: 'spline',
                            animation: Highcharts.svg,
                            marginRight: 10,
                            events: {load: function () {series = this.series[0];}}
                           
                        },
                    title:{text: 'Presion BMP180'},
                    xAxis:{tickPixelInterval: 150},
                    yAxis:{title: {text: 'Presion'},
                        plotLines: [{value: 0,width: 1,color: '#FF0000'}]
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.series.name + '</b><br/>' +
                                Highcharts.numberFormat(this.y, 2) + '<br/>';
                            }
                    },
                    legend: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{ name: 'Presion BMP180', data:DatosRecuperadosp,  color: "#FF0000", stickyTracking: false}]
                });
              
         
       }});
       });
       var c=0;
       var d=600;
       setInterval(function () {
        
             $.get( "consultaPre.php", function( UltimosDatosp ) {
                 var varlocalx3=parseFloat(UltimosDatosp[c].x);
                  
                 c=c+1;
                        
                 
              if(((getvalue3()!=varlocalx3) || (getvalue3()===varlocalx3))){
                 
                 series.addPoint([d,varlocalx3], true, true);
                 setvalue3(varlocalx3);
                 d=d+100;
                 
             }

        });}, 3000);
        function setvalue3(x){ultimovalue3=x;}
        function getvalue3(){return ultimovalue3;}
}); 
    </script>
</body>
</html>